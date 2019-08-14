<?php

namespace App\Http\Controllers\api\Admin;

use App\Http\Requests\Admin\Statuses\ValidateRequest;
use App\Models\Status;
use App\Services\JsonService;
use App\Services\LanguagesService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Status as StatusResource;

class StatusController extends Controller
{
    public $model;
    public $json;
    public $languages;

    public function __construct(
        Status $status,
        JsonService $jsonService,
        LanguagesService $languagesService
    )
    {
        $this->model = $status;
        $this->json = $jsonService;
        $this->languages = $languagesService;
    }
    public function index($cLang): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return StatusResource::collection($this->model->getStatusNameByLang($cLang));
    }

    public function store(ValidateRequest $request): StatusResource
    {
        $status = $this->model->addNewStatus($request->all());

        return new StatusResource($status);
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
