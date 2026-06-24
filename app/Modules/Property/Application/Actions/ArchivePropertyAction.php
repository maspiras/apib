<?php

namespace App\Modules\Property\Application\Actions;

use App\Modules\Property\Domain\Enums\PropertyStatus;
use App\Modules\Property\Domain\Events\PropertyArchived;
use App\Modules\Property\Domain\Exceptions\PropertyNotFoundException;
use App\Modules\Property\Domain\Repositories\PropertyRepository;
use Illuminate\Support\Facades\DB;

class ArchivePropertyAction
{
    public function __construct(
        protected PropertyRepository $properties
    ) {
    }

    public function execute(int $propertyId)
    {
        return DB::transaction(function () use ($propertyId) {

            $property = $this->properties->find($propertyId);

            if (! $property) {
                throw new PropertyNotFoundException();
            }

            if (
                $property->status ===
                PropertyStatus::Archived->value
            ) {
                return $property;
            }

            $property->status = PropertyStatus::Archived->value;

            $property->archived_at = now();

            $property->save();

            event(
                new PropertyArchived(
                    propertyId: $property->id
                )
            );

            return $property->fresh();
        });
    }
}