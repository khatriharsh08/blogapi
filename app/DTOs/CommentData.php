<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class CommentData
{
    public function __construct(
        public string $content,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            content: $data['content'],
        );
    }
}
