<?php
class AnswerFinder {
    private $embeddingGenerator;
    private $vectorQuery;
    private $responseGenerator;
    private $promptConstructor;
    private $urlSanitizer;

    public function __construct(
        EmbeddingGeneratorInterface $embeddingGenerator,
        VectorQueryInterface $vectorQuery,
        ResponseGeneratorInterface $responseGenerator,
        PromptConstructor $promptConstructor,
        UrlSanitizer $urlSanitizer
    ) {
        $this->embeddingGenerator = $embeddingGenerator;
        $this->vectorQuery = $vectorQuery;
        $this->responseGenerator = $responseGenerator;
        $this->promptConstructor = $promptConstructor;
        $this->urlSanitizer = $urlSanitizer;
    }

    public function find_answer(string $question): array {
        $text = preprocess_text($question);
        $questionVector = $this->embeddingGenerator->generate_embedding($text);
        $vectorResponse = $this->vectorQuery->query($questionVector);
        
        $mostRelevant = $vectorResponse['matches'][0] ?? [];
        $mostRelevantText = $this->extract_relevant_text($mostRelevant);
        
        $uniqueUrls = $this->urlSanitizer->sanitize_urls($vectorResponse['matches'] ?? []);
        $prompt = $this->promptConstructor->construct($question, $mostRelevantText);
        $answer = $this->responseGenerator->generate_response($prompt);

        //store the result
        zara_store_conversation(zara_get_cookie_session_id(),$question, $mostRelevantText, $answer);


        return [
            'text' => $answer,
            'sourceDocuments' => $uniqueUrls
        ];
    }

    private function extract_relevant_text(array $match): string {
        return isset($match['metadata']['text']) 
            ? sanitize_text_field($match['metadata']['text'])
            : '';
    }
}