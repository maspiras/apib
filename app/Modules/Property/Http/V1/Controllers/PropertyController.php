<?php

namespace App\Modules\Property\Http\V1\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Modules\Property\Application\Actions\CreatePropertyAction;
use App\Modules\Property\Application\Actions\UpdatePropertyAction;
use App\Modules\Property\Application\Actions\ArchivePropertyAction;
use App\Modules\Property\Application\DTOs\CreatePropertyData;
use App\Modules\Property\Application\DTOs\UpdatePropertyData;
use App\Modules\Property\Http\V1\Requests\StorePropertyRequest;
use App\Modules\Property\Http\V1\Requests\UpdatePropertyRequest;
use App\Modules\Property\Http\V1\Resources\PropertyResource;

use App\Modules\Property\Application\Queries\GetPropertyQuery;
use App\Modules\Property\Application\Queries\ListPropertiesQuery;
use App\Modules\Property\Application\Queries\GetPropertyRoomsQuery;

class PropertyController extends Controller
{
    public function index(){
        return now().' index kano';
    }
    public function store(
        StorePropertyRequest $request,
        CreatePropertyAction $action
    ) {
        $dto = CreatePropertyData::fromArray(
            $request->validated()
        );

        $property = $action->execute($dto);

        return new PropertyResource($property);
    }

    public function update(
        UpdatePropertyRequest $request,
        int $id,
        UpdatePropertyAction $action
    ) {
        $dto = UpdatePropertyData::fromArray(
            $request->validated()
        );

        $property = $action->execute($id, $dto);

        return new PropertyResource($property);
    }

    public function show(int $property, GetPropertyQuery $query){
        return response()->json($query->execute($property));        
    }

    public function archive(int $id, ArchivePropertyAction $action){
      $property = $action->execute($id);
      return response()->json(['message' => 'Property archived successfully.',
      'data' => $property,]);
    }

    public function destroy(
        int $id,
        DeletePropertyAction $action
    ) {
        $action->execute($id);

        return response()->noContent();
    }

    public function myProperties(
    Request $request,
    ListPropertiesQuery $query
) {
    return response()->json(
        $query->execute(
            auth()->id(),
            $request->get('page', 1),
            $request->get('per_page', 15)
        )
    );
}

    public function rooms(
        int $propertyId,
        Request $request,
        GetPropertyRoomsQuery $query
    ) {
        return response()->json(
            $query->execute(
                $propertyId,
                $request->only(['status', 'adults', 'children'])
            )
        );
    }

    public function sample(){
        return 'gago';
    }
}