<?php

namespace App\Http\Controllers\api\Admin;

use App\Http\Requests\Admin\ModelingOrders\ValidateRequest;
use App\Models\Modeling;
use App\Http\Controllers\Controller;
use App\Http\Resources\Modeling as ModelingResource;

class ModelingOrderController extends Controller
{
    public $model;

    public function __construct( Modeling $modeling )
    {
        $this->model = $modeling;
    }
    public function index($cLang): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return ModelingResource::collection($this->model->getConfirmModelingOrdersByLang($cLang));
    }

    public function store(ValidateRequest $request): ModelingResource
    {
        return new ModelingResource($this->model::addAdminOrder($request->all()));
    }

    public function show(Modeling $modeling): ModelingResource
    {
        return new ModelingResource($this->model->getOrder($modeling));
    }

    public function update(ValidateRequest $request, Modeling $modeling): ModelingResource
    {
        return new ModelingResource($this->model->editAdminOrder($request->all(), $modeling));
    }

    public function destroy(Modeling $modeling)
    {
        try {
            $modeling->delete();
        } catch (\Exception $e) {
        }

        return response()->json(null, 204);
    }
}
