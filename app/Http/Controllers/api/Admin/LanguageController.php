<?php

namespace App\Http\Controllers\api\Admin;

use App\Http\Requests\Admin\Languages\ValidateRequest;
use App\Http\Resources\Language as LanguageResource;
use App\Models\Language;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class LanguageController extends Controller
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

    public function show(Language $language): LanguageResource
    {
        return new LanguageResource($language);
    }

    public function store(ValidateRequest $request): LanguageResource
    {
        $language = $this->model::create($request->all());

        return new LanguageResource($language);
    }

    public function update(ValidateRequest $request, Language $language): LanguageResource
    {
        return new LanguageResource($language->editLanguage($request, $language));
    }

    public function destroy(Language $language)
    {
        $language->delete();

        return response()->json(null, 204);
//        return response()->json([
//            'message' => Lang::get('messages.language_deleted')],
//            204);
    }

    public function actives()
    {
        return response()->json($this->model->getActiveLanguages());
    }

}
