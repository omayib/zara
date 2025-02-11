<?php
class CompletionEngine{
    public static function create(string $engineType): ResponseGeneratorInterface {
        switch(strtolower($engineType)){
            case 'openai':
                return new OpenAiEngine();
            default:
                throw new Exception("Embedding engine '{$engineType}' is not recognized");
        }
    }
}