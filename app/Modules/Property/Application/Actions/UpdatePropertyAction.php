<?php

namespace App\Modules\Property\Application\Actions;

use App\Modules\Property\Application\DTOs\UpdatePropertyData;
use App\Modules\Property\Domain\Repositories\PropertyRepository;

class UpdatePropertyAction
{
    public function __construct(
        protected PropertyRepository $properties
    ) {
    }

    public function execute(
        int $propertyId,
        UpdatePropertyData $data
    ) {
        $property = $this->properties->findOrFail($propertyId);

        return $this->properties->update(
            $property,
            [
                'name'        => $data->name,
                'description' => $data->description,
            ]
        );
    }
}