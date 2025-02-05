<?php
class IndexingContents{
    private $embeddingGenerator;
    private $vectorQuery;

    public function __construct(
        EmbeddingGeneratorInterface $embeddingGenerator,
        VectorQueryInterface $vectorQuery
    ){
        $this->embeddingGenerator = $embeddingGenerator;
        $this->vectorQuery = $vectorQuery;
    }

    public function upserting_single_post($post_id){
        $content = get_post($post_id);
        $urlContent = get_permalink($content->ID);
        $htmlContent = apply_filters('the_content', $content->post_content);
        $cleanedText = cleanHtml($htmlContent);
        $sentences = splitWords($cleanedText);
        $chunks = createContextChunks($sentences, 10, 5);
        // error_log( "Chunks ".count($chunks). ":\n" . print_r($chunks,true));
        foreach($chunks as $chunk){
            $vectors = $this->embeddingGenerator->generate_embedding($chunk);
            $prepare = [
                "id"=> hash('sha1',$chunk),
                "vector"=>$vectors,
                "meta"=>[
                    "source"=>$urlContent,
                    "text"=>$chunk
                ]
            ];
            error_log(json_encode($prepare));
            $this->vectorQuery->upsert($prepare["id"],$prepare["vector"],$prepare["meta"]);
        }
        wp_reset_postdata();
    }
}
