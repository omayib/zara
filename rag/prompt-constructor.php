<?php
class PromptConstructor {
    public function construct(string $userQuery, string $relevantText): string {
        $context = "Here are the most relevant articles based on the user's query:\n\n";
        $context .= $relevantText;
        return $context . "User's Question: $userQuery\n\nProvide a concise and informative response based on the above context.";
    }
}