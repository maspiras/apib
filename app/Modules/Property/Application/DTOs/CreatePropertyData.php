<?php

namespace App\Modules\Property\Application\DTOs;

class CreatePropertyData
{
    public function __construct(
        public readonly int $ownerId,
        public readonly string $name,
        public readonly string $slug,
        public readonly string $description,
    ) {
    }

    public static function fromArray(
        array $data
    ): self {
        return new self(
            ownerId: $data['owner_id'],
            name: $data['name'],
            slug: $data['slug'],
            description: $data['description'] ?? '',
        );
    }
}