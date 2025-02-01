<?php

/**
 * 
 * sample question
 * What does the author suggest a new WordPress user should do with this page?
 * What makes this "Sample Page" different from a blog post?
 * What are two examples given to introduce potential site visitors?
 */
function looking_for_answer($question){
    // error_log(print_r($metadata,true));
    $text = preprocess_text($question);

    //2. embedding
    $question_in_vector = generate_embeddings($text);

    //3. looking for similar vector value
    $response_vector = query_vectors_in_pinecone($question_in_vector);
    $most_relevant = $response_vector['matches'][0] ?? [];

    $most_relevan_text = isset($most_relevant['metadata']['text']) 
        ? sanitize_text_field($most_relevant['metadata']['text'])
        : '';

    // Extract and sanitize URLs
    $urls = [];
    if (isset($response_vector['matches']) && is_array($response_vector['matches'])) {
        foreach ($response_vector['matches'] as $match) {
            if (isset($match['metadata']['source'])) {
                $cleanUrl = filter_var($match['metadata']['source'], FILTER_SANITIZE_URL);
                if ($cleanUrl) {
                    $urls[] = $cleanUrl;
                }
            }
        }
    }

    // Remove duplicate URLs while preserving order
    $uniqueUrls = array_values(array_unique($urls));

    //4. construct prompt
    $prompt = construct_prompt($question,$most_relevan_text);

    //5. generate gpt response
    $answer = generate_gpt_response($prompt);
    $response = [
        'text' => $answer,
        'sourceDocuments' => $uniqueUrls
    ];

    return $response;
}

function construct_prompt($user_query, $relevant_articles) {
    $context = "Here are the most relevant articles based on the user's query:\n\n";
    $context .= $relevant_articles;
    $prompt = $context . "User's Question: $user_query\n\nProvide a concise and informative response based on the above context.";
    return $prompt;
}
