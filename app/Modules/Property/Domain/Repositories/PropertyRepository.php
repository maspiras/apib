<?php

namespace App\Modules\Property\Domain\Repositories;

interface PropertyRepository
{
    public function find(int $id): array;

    public function search(array $filters =[]): array;

    public function findOrFail(int $id): array;

    public function create(array $data): array;

    public function update(int $id, array $data): array;

    public function delete(int $id): bool;

    public function publish(int $id): array;

    public function archive(int $id): array;

    public function getRooms(int $propertyId, array $filters=[]): array;
}