<?php

namespace App\Http\Controllers\api\Pages;

use App\Http\Resources\Country as CountryResource;
use App\Models\Country;
use App\Http\Controllers\Controller;

class languagesController extends Controller
{
    public $model;

    public function __construct( Country $country )
    {
        $this->model = $country;
    }
    public function index($cLang): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return CountryResource::collection($this->model->getCountryListByLanguages($cLang));

    }
}
