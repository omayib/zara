<?php
interface EmbeddingGeneratorInterface{
    public function generate_embedding(string $text):array;
}