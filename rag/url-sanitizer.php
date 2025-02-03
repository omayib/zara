<?php
class UrlSanitizer {
    public function sanitize_urls(array $matches): array {
        $urls = [];
        foreach ($matches as $match) {
            if (isset($match['metadata']['source'])) {
                $cleanUrl = filter_var($match['metadata']['source'], FILTER_SANITIZE_URL);
                if ($cleanUrl) {
                    $urls[] = $cleanUrl;
                }
            }
        }
        return array_values(array_unique($urls));
    }
}