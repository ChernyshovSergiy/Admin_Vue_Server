<?php

namespace App\Http\Controllers\api\Pages;

use App\Http\Resources\Language as LanguageResource;
use App\Models\Language;
use App\Http\Controllers\Controller;

class languagesController extends Controller
{
    public $model;

    public function __construct(Language $language)
    {
        $this->model = $language;
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return LanguageResource::collection($this->model::all());
    }
}
