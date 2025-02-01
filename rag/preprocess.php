<?php
function preprocess_text(string $pieces_of_text): string {
    // 1. Clean text
    $text = preg_replace('/\s+/', ' ', trim($pieces_of_text));
    $text = strip_tags($text);
    
    // 2. Normalize case and punctuation
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);
    
    // 3. Expand contractions (optional)
    $contractions = [
        "/don't/i" => "do not",
        "/can't/i" => "cannot",
        "/i'm/i" => "I am",
        "/you're/i" => "you are",
        "/he's/i" => "he is",
        "/she's/i" => "she is",
        "/it's/i" => "it is",
        "/we're/i" => "we are",
        "/they're/i" => "they are",
        "/i've/i" => "I have",
        "/you've/i" => "you have",
        "/we've/i" => "we have",
        "/they've/i" => "they have",
        "/i'll/i" => "I will",
        "/you'll/i" => "you will",
        "/he'll/i" => "he will",
        "/she'll/i" => "she will",
        "/it'll/i" => "it will",
        "/we'll/i" => "we will",
        "/they'll/i" => "they will",
        "/isn't/i" => "is not",
        "/aren't/i" => "are not",
        "/wasn't/i" => "was not",
        "/weren't/i" => "were not",
        "/won't/i" => "will not",
        "/wouldn't/i" => "would not",
        "/couldn't/i" => "could not",
        "/shouldn't/i" => "should not",
        "/mustn't/i" => "must not",
        "/let's/i" => "let us",
        "/that's/i" => "that is",
        "/what's/i" => "what is",
        "/there's/i" => "there is",
        "/here's/i" => "here is",
        "/how's/i" => "how is",
        "/where's/i" => "where is",
        "/who's/i" => "who is",
        "/why's/i" => "why is"
    ];
    $text = preg_replace(array_keys($contractions), array_values($contractions), $text);
    
    return $text;
}