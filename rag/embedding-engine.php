<?php
class EmbeddingEngine{
    public static function create(string $engineType): EmbeddingGeneratorInterface {
        switch(strtolower($engineType)){
            case 'openai':
                return new OpenAiEngine();
            default:
                throw new Exception("Embedding engine '{$engineType}' is not recognized");
        }
    }
}