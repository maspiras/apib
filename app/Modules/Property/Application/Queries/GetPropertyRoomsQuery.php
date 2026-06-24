<?php

namespace App\Modules\Property\Application\Queries;

use App\Modules\Property\Domain\Repositories\PropertyRepository;    

class GetPropertyRoomsQuery
{
    public function __construct(
        protected PropertyRepository $properties
    ) {
    }

    public function execute(
        int $propertyId,
        array $filters = []
    ): array {
        return $this->properties->getRooms(
            $propertyId,
            $filters
        );
    }
}