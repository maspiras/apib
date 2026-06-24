<?php

namespace App\Modules\Property\Application\Queries;

use App\Modules\Property\Domain\Repositories\PropertyRepository;

class GetPropertyQuery
{
    public function __construct(
        protected PropertyRepository $properties
    ) {
    }

    public function execute(
        int $propertyId
    ): array {
        return $this->properties->find(
            $propertyId
        );
    }
}