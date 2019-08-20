<?php

namespace App\Http\Controllers\api\Admin;

use App\Http\Requests\Admin\Menus\ValidateRequest;
use App\Http\Resources\Menu as MenuResource;
use App\Models\Menu;
use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    public $model;

    public function __construct( Menu $menu )
    {
        $this->model = $menu;
    }

    public function index($cLang): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return MenuResource::collection($this->model->getMenuByLang($cLang));
    }

    public function store(ValidateRequest $request)
    {
        //
    }

    public function show(Menu $menu): MenuResource
    {
        return new MenuResource($this->model->getMenu($menu));
    }

    public function update(ValidateRequest $request, Menu $menu): MenuResource
    {
        return new MenuResource($this->model->editMenu($request, $menu));
    }

    public function destroy(Menu $menu)
    {
        //
    }
}
