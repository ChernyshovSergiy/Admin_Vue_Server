<?php

namespace App\Http\Controllers\api\Admin;

use App\Models\Modeling;
use Illuminate\Http\Request;
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

    public function store(Request $request)
    {
        //
    }

    public function show(Modeling $modeling)
    {
        //
    }

    public function update(Request $request, Modeling $modeling)
    {
        //
    }

    public function destroy(Modeling $modeling)
    {
        //
    }
}
