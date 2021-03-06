<?php

namespace App\Http\Controllers\api\Admin;

use App\Http\Requests\Admin\PrintingOrders\ValidateRequest;
use App\Models\Printing;
use App\Http\Controllers\Controller;
use App\Http\Resources\Printing as PrintingResource;

class PrintingOrderController extends Controller
{
    public $model;

    public function __construct( Printing $printing )
    {
        $this->model = $printing;
    }

    public function index($cLang): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return PrintingResource::collection($this->model->getConfirmPrintingOrdersByLang($cLang));
    }

    public function store(ValidateRequest $request): PrintingResource
    {
        return new PrintingResource($this->model::adminAddNewPrintingOrder($request->all()));
    }

    public function show(Printing $printing): PrintingResource
    {
        return new PrintingResource($this->model->getOrder($printing));
    }

    public function update(ValidateRequest $request, Printing $printing)
    {
        return new PrintingResource($this->model->editAdminOrder($request->all(), $printing));
    }

    public function destroy(Printing $printing)
    {
        //
    }
}
