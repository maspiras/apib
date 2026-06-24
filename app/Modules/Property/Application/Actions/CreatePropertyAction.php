<?php

namespace App\Modules\Property\Application\Actions;

use App\Modules\Property\Application\DTOs\CreatePropertyData;
use App\Modules\Property\Domain\Repositories\PropertyRepository;

class CreatePropertyAction
{
    public function __construct(
        protected PropertyRepository $properties
    ) {
    }

    public function execute(CreatePropertyData $data)
    {
        return $this->properties->create([
            'owner_id'   => $data->ownerId,
            'name'       => $data->name,
            'slug'       => $data->slug,
            'description'=> $data->description,
            'status'     => 'draft',
        ]);
    }
}