<?php
class PineconeEngine implements VectorQueryInterface {
    private $api_key;
    private $api_url_upsert;
    private $api_url_query;

    public function __construct() {
        $this->api_key = get_option('pinecone_options')['pinecone_key'];
        $this->api_url_upsert = get_option('pinecone_options')['pinecone_index']."/vectors/upsert";
        $this->api_url_query = get_option('pinecone_options')['pinecone_index']."/query";
    }
    public function query(array $vector): array {
        $response = wp_remote_post($this->api_url_query, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Api-Key' => $this->api_key,
            ],
            'body' => json_encode([
                'vector' => $vector,
                'topK' => 3,
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
    public function upsert($id, $embedding, $metadata = []):string{
        $response = wp_remote_post($this->api_url_upsert, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Api-Key' => $this->api_key,
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
}