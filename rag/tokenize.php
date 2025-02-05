<?php
function external_custom_function($post_id){

    $post = get_post($post_id);
    $urlContent = get_permalink($post->ID);
    $htmlContent = apply_filters('the_content', $post->post_content);
    error_log("post_content ".$htmlContent);


    // Processing pipeline
    $cleanedText = cleanHtml($htmlContent);
    $sentences = splitWords($cleanedText);
    $chunks = createContextChunks($sentences, 10, 5);
    // error_log( "Chunks ".count($chunks). ":\n" . print_r($chunks,true));
    foreach($chunks as $chunk){
        $vectors = generate_embeddings($chunk);
        $prepare = [
            "id"=> hash('sha1',$chunk),
            "vector"=>$vectors,
            "meta"=>[
                "source"=>$urlContent,
                "text"=>$chunk
            ]
        ];
        error_log(json_encode($prepare));
        store_vector_in_pinecone($prepare["id"],$prepare["vector"],$prepare["meta"]);
    }
}
function read_all_content(){
    $all_content = get_posts([
        'post_type' => ['post','page'],
        'post_status'=> 'publish',
        'posts_per_page'=>-1 
    ]);
    /**
     * report = [
     *      'post_id'=>34,
     *      'post_type'=>'post',
     *      'source'=>'https://wwww.sample.com/pages',
     *      'chunks_total'=>456,
     *      'hashes'=>[]
     * ]
     */
    foreach ($all_content as $content){
        $htmlContent = apply_filters('the_content', $content->post_content);
        $urlContent = get_permalink($content->ID);
        // Processing pipeline
        $cleanedText = cleanHtml($htmlContent);
        $sentences = splitWords($cleanedText);
        $chunks = createContextChunks($sentences, 10, 5);

        // error_log( "Chunks ".count($chunks). ":\n" . print_r($chunks,true));

        foreach($chunks as $chunk){
            $vectors = generate_embeddings($chunk);
            $prepare = [
                "id"=> hash('sha1',$chunk),
                "vector"=>$vectors,
                "meta"=>[
                    "source"=>$urlContent,
                    "text"=>$chunk
                ]
            ];
            // error_log(json_encode($prepare));
            // store_vector_in_pinecone($prepare["id"],$prepare["vector"],$prepare["meta"]);
        }
    }
    wp_reset_postdata(); 
}

function cleanHtml($content) {
    $content = strip_tags($content, '<br><p>');
    $content = html_entity_decode($content);
    $content = preg_replace('/\s+/', ' ', $content);
    $content = strip_tags($content);
    $content = preprocess_text($content);
    return trim($content);
}

function splitSentences($text) {
    $pattern = '/(?<=[.!?])\s+(?=[A-Z])/u';
    return preg_split($pattern, $text, -1, PREG_SPLIT_NO_EMPTY);
}
function splitWords($text) {
    preg_match_all('/\b[\p{L}\']+\b[\p{L}\.,!?]*/u', $text, $matches);
    return $matches[0];
}
function createContextChunks($words, $chunkSize, $chunkOverlap) {
    $chunks = [];
    $current = [];
    $sentenceEnders = ['.', '!', '?'];
    
    foreach ($words as $word) {
        $current[] = $word;
        $lastChar = substr($word, -1);
        
        if (in_array($lastChar, $sentenceEnders) && count($current) >= $chunkSize) {
            $chunks[] = implode(' ', $current);
            $current = array_slice($current, -$chunkOverlap);
        }
    }
    
    if (!empty($current)) {
        $chunks[] = implode(' ', $current);
    }
    
    return $chunks;
}

function get_overlapping_word_chunks( $text, $chunkSize = 10, $overlap = 5 ) {
    // Split the text into an array of words
    // Using PREG_SPLIT_NO_EMPTY removes empty entries
    // (i.e., in case of multiple spaces, etc.)
    $words = preg_split( '/\s+/', trim( $text ), -1, PREG_SPLIT_NO_EMPTY );
    
    // If we have fewer words than one chunk, just return the entire text as a single chunk
    if ( count( $words ) <= $chunkSize ) {
        return [ implode( ' ', $words ) ];
    }
    
    // How many words to move ahead for each subsequent chunk
    $step   = $chunkSize - $overlap;  // e.g. 10 - 5 = 5
    $chunks = [];
    
    // Use a for-loop, incrementing by $step each time
    for ( $i = 0; $i < count( $words ); $i += $step ) {
        // Extract a slice of $chunkSize words starting at index $i
        $chunk = array_slice( $words, $i, $chunkSize );
        
        // If the chunk has fewer than $chunkSize words, decide whether to include partial
        // chunk or break out. Here we break to ensure each chunk is exactly 10 words.
        if ( count( $chunk ) < $chunkSize ) {
            break;
        }
        
        // Join the words back into a string
        $chunks[] = implode( ' ', $chunk );
    }
    
    return $chunks;
}