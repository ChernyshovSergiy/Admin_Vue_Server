<?php

namespace App\Http\Controllers\api\Admin;

use App\Http\Requests\Admin\Statuses\ValidateRequest;
use App\Models\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\Status as StatusResource;

class StatusController extends Controller
{
    public $model;

    public function __construct( Status $status )
    {
        $this->model = $status;
    }

    public function index($cLang): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return StatusResource::collection($this->model->getStatusNameByLang($cLang));
    }

    public function store(ValidateRequest $request): StatusResource
    {
        return new StatusResource($this->model->addNewStatus($request->all()));
    }

    public function show(Status $status): StatusResource
    {
        return new StatusResource($this->model->getStatus($status));
    }

    public function update(ValidateRequest $request, Status $status): StatusResource
    {
        return new StatusResource($this->model->editStatus($request->all(), $status));
    }

    public function destroy(Status $status)
    {
        try {
            $status->delete();
        } catch (\Exception $e) {
        }

        return response()->json(null, 204);
    }
}
