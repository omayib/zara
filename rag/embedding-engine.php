<?php
class EmbeddingEngine{
    public static function create(string $engineType): EmbeddingGeneratorInterface {
        switch(strtolower($engineType)){
            case 'openai':
                return new OpenAiEngine();
            default:
                throw new Exception(
                    sprintf(
                        "Embedding engine '%s' is not recognized",
                        esc_html( $engineType )
                    )
                );
        }
    }
}