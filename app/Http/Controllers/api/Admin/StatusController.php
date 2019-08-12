<?php

namespace App\Http\Controllers\api\Admin;

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

    public function store(Request $request)
    {
        //
    }

    public function show(Status $status)
    {
        //
    }

    public function update(Request $request, Status $status)
    {
        //
    }

    public function destroy(Status $status)
    {
        //
    }
}
