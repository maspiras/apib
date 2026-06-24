<?php

namespace App\Modules\Property\Application\Queries;

class GetAvailableRoomsQuery
{
    public function __construct(
        public readonly int $propertyId,
        public readonly string $checkIn,
        public readonly string $checkOut,
        public readonly int $adults = 1,
        public readonly int $children = 0,
        public readonly ?int $rooms = 1,
    ) {
    }

    public static function fromRequest(
        int $propertyId,
        array $data
    ): self {
        return new self(
            propertyId: $propertyId,
            checkIn: $data['check_in'],
            checkOut: $data['check_out'],
            adults: $data['adults'] ?? 1,
            children: $data['children'] ?? 0,
            rooms: $data['rooms'] ?? 1,
        );
    }
}