<?php
class VectorEngine{
    public static function create(string $engineType): VectorQueryInterface {
        switch(strtolower($engineType)){
            case 'pinecone':
                return new PineconeEngine();
            default:
                throw new Exception(
                    sprintf(
                        "Vector engine '%s' is not recognized",
                        esc_html( $engineType )
                    )
                );
        }
    }
}