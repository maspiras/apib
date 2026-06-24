<?php

namespace App\Modules\Property\Application\Queries;

use App\Modules\Property\Domain\Repositories\PropertyRepository;    

class ListPropertiesQuery
{
    public function __construct(
        protected PropertyRepository $properties
    ) {
    }

    public function execute(
        int $ownerId,
        int $page = 1,
        int $perPage = 15
    ): array {
        return $this->properties->search([
            'owner_id' => $ownerId,
            'page' => $page,
            'per_page' => $perPage,
        ]);
    }
}