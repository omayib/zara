<?php

function generate_embeddings($content) {
    $api_key = get_option('openai_options')['openai_key'];
    $api_url = 'https://api.openai.com/v1/embeddings';
    // $api_key = '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';

    $response = wp_remote_post($api_url, [
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ],
        'body' => json_encode([
            'model' => 'text-embedding-ada-002',
            'input' => $content,
        ]),
    ]);

    if (is_wp_error($response)) {
        return null;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    return $data['data'][0]['embedding'] ?? null;
}

function generate_gpt_response($prompt) {
    $api_key = get_option('openai_options')['openai_key'];
    $api_url = 'https://api.openai.com/v1/chat/completions';
    // $api_key = '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';

    $response = wp_remote_post($api_url, [
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
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

