<?php
interface VectorQueryInterface{
    public function query(array $vector):array;
    public function upsert(string $id, array $embedding, array $metadata = []): string;
}