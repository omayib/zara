<?php
class OpenAiEngine implements EmbeddingGeneratorInterface, ResponseGeneratorInterface{
    private $api_key;
    private $api_url_embedding;
    private $api_url_completion;

    public function __construct() {
        $this->api_key = get_option('zara_options')['openai_key'];
        $this->api_key_completion = get_option('zara_options')['openai_key_completion'];
        $this->api_url_embedding = 'https://api.openai.com/v1/embeddings';
        $this->api_url_completion = 'https://api.openai.com/v1/chat/completions';
    }
    public function generate_embedding(string $text): array{
        $response = wp_remote_post($this->api_url_embedding, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key,
            ],
            'body' => json_encode([
                'model' => 'text-embedding-ada-002',
                'input' => $text,
            ]),
        ]);
    
        if (is_wp_error($response)) {
            return null;
        }
    
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
    
        return $data['data'][0]['embedding'] ?? null;
    }
    public function generate_response(string $prompt): string{
        $response = wp_remote_post($this->api_url_completion, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key_completion,
            ],
            'body' => json_encode([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 500,
                'temperature' => 0.7,
            ]),
        ]);
    
        if (is_wp_error($response)) {
            return 'Error generating response: ' . $response->get_error_message();
        }
    
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
    
        return $data['choices'][0]['message']['content'] ?? 'No response generated.';
    }
}