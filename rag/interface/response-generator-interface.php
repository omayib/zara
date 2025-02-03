<?php
interface ResponseGeneratorInterface {
    public function generate_response(string $prompt): string;
}