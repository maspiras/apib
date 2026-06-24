<?php

namespace App\Modules\Property\Infrastructure\Repositories;

#use App\Services\Api\BookcomfyApiClient;
use App\Shared\Api\BookcomfyApiClient;
use App\Modules\Property\Domain\Repositories\PropertyRepository;

class ApiPropertyRepository implements PropertyRepository
{
    public function __construct(
        protected BookcomfyApiClient $api
    ) {
    }

    public function find(int $id): array
    {
        return $this->api->get(
            "/v1/properties/{$id}"
        );
    }

    public function search(
        array $filters = []
    ): array {
        return $this->api->get(
            '/v1/properties',
            $filters
        );
    }

    public function findOrFail(int $id): array{
        return $this->api->get(
            "/v1/properties/{$id}"
        );
    }

    public function create(
        array $data
    ): array {
        return $this->api->post(
            '/v1/properties',
            $data
        );
    }

    public function update(
        int $id,
        array $data
    ): array {
        return $this->api->put(
            "/v1/properties/{$id}",
            $data
        );
    }

    public function delete(
        int $id
    ): bool {
        $this->api->delete(
            "/v1/properties/{$id}"
        );

        return true;
    }

    public function publish(
        int $id
    ): array {
        return $this->api->post(
            "/v1/properties/{$id}/publish"
        );
    }

    public function archive(
        int $id
    ): array {
        return $this->api->post(
            "/v1/properties/{$id}/archive"
        );
    }

    public function getRooms(int $propertyId, array $filters=[]): array{
        return $this->api->get("/v1/properties/{$propertyId}/rooms", array_filter($filters));
    }
}