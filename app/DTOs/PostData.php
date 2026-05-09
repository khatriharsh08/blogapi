<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class PostData
{
    public function __construct(
        public string $title,
        public string $content,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? '',
            content: $data['content'] ?? '',
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'content' => $this->content,
        ]);
    }
}
