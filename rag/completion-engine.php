<?php
class CompletionEngine{
    public static function create(string $engineType): ResponseGeneratorInterface {
        switch(strtolower($engineType)){
            case 'openai':
                return new OpenAiEngine();
            default:
                throw new Exception(
                    sprintf(
                        "Completion engine '%s' is not recognized",
                        esc_html( $engineType )
                    )
                );
        }
    }
}