<?php

namespace App\Modules\Property\Application\Actions;

use App\Modules\Property\Domain\Repositories\PropertyRepository;
use App\Modules\Property\Domain\Events\PropertyPublished;

class PublishPropertyAction
{
    public function __construct(
        protected PropertyRepository $properties
    ) {
    }

    public function execute(int $propertyId)
    {
        $property = $this->properties->findOrFail(
            $propertyId
        );

        if (empty($property->name)) {
            throw new \DomainException(
                'Property name is required.'
            );
        }

        if (empty($property->description)) {
            throw new \DomainException(
                'Property description is required.'
            );
        }

        $property->status = 'active';

        $property->save();

        event(
            new PropertyPublished(
                $property->id
            )
        );

        return $property;
    }
}