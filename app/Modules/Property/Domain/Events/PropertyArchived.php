<?php

namespace App\Modules\Property\Domain\Events;

class PropertyArchived
{
    public function __construct(
        public readonly int $propertyId
    ) {
    }
}