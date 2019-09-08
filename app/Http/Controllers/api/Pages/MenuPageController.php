<?php

namespace App\Http\Controllers\api\Pages;

use App\Http\Resources\Menu as MenuResource;
use App\Models\Menu;
use App\Services\JsonService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MenuPageController extends Controller
{
    public $model;
    public $json;

    public function __construct(Menu $menu, JsonService $jsonService)
    {
        $this->model = $menu;
        $this->json = $jsonService;
    }

//    public function menu(Request $request){
//        $curLang = $request->get('language');
////        $menus = $this->json->build($this->model ,'title');
//        $menus = $this->json->buildMenuOneLang($this->model ,'title', $curLang);
//        return response()->json([
//            'massage' => 'Data menus delivered',
//            'data' => $menus
//        ]);
//    }

    public function menu($cLang): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return MenuResource::collection($this->json->buildMenuOneLang($this->model, 'title', $cLang));
    }
}
