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
        $chunks = get_overlapping_word_chunks($cleanedText,20,5);
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
            $this->vectorQuery->upsert($prepare["id"],$prepare["vector"],$prepare["meta"]);
        }
        wp_reset_postdata();
        return ['post_id'=>$post_id,'chunk_total'=>count($chunks)];
    }
}
