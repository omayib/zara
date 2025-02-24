<?php

function store_vector_in_pinecone($id, $embedding, $metadata = []) {
    $api_key = get_option('pinecone_options')['pinecone_key'];
    $api_url = get_option('pinecone_options')['pinecone_index']."/vectors/upsert";

    $response = wp_remote_post($api_url, [
        'headers' => [
            'Content-Type' => 'application/json',
            'Api-Key' => $api_key,
        ],
        'body' => json_encode([
            'vectors' => [
                [
                    'id' => $id,
                    'values' => $embedding,
                    'metadata' => $metadata,
                ],
            ],
        ]),
    ]);

    if (is_wp_error($response)) {
        return 'Error storing vector: ' . $response->get_error_message();
    }

    return wp_remote_retrieve_body($response);
}

function query_vectors_in_pinecone($query_vector, $top_k = 5) {

    $api_key = get_option('pinecone_options')['pinecone_key'];
    $api_url = get_option('pinecone_options')['pinecone_index']."/query";

    $response = wp_remote_post($api_url, [
        'headers' => [
            'Content-Type' => 'application/json',
            'Api-Key' => $api_key,
        ],
        'body' => json_encode([
            'vector' => $query_vector,
            'topK' => $top_k,
            'includeMetadata' => true,
        ]),
    ]);

    if (is_wp_error($response)) {
        return 'Error querying vectors: ' . $response->get_error_message();
    }
    $body = wp_remote_retrieve_body($response);
    $bodyJson = json_decode($body, true);
    return $bodyJson;
}
