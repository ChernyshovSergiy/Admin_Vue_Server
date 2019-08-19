<?php

namespace App\Http\Controllers\api\Admin;

use App\Http\Requests\Admin\Contents\ValidateRequest;
use App\Models\Content;
use App\Http\Controllers\Controller;
use App\Http\Resources\Content as ContentResource;

class ContentController extends Controller
{
    public $model;

    public function __construct( Content $content )
    {
        $this->model = $content;
    }

    public function index($cLang): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return ContentResource::collection($this->model->getContentByLang($cLang));
    }

    public function store(ValidateRequest $request): ContentResource
    {
        return new ContentResource($this->model->addNewContent($request));
    }

    public function show(Content $content): ContentResource
    {
        return new ContentResource($this->model->getContent($content));
    }

    public function update(ValidateRequest $request, Content $content): ContentResource
    {
        return new ContentResource($this->model->editContent($request, $content));
    }

    public function destroy(Content $content)
    {
        try {
            $content->delete();
        } catch (\Exception $e) {
        }

        return response()->json(null, 204);
    }
}
