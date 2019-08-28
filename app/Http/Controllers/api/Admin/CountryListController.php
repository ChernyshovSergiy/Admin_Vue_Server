<?php

namespace App\Http\Controllers\api\Admin;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Country as CountryResource;

class CountryListController extends Controller
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

    public function store(Request $request)
    {
        //
    }

    public function show(Country $country)
    {
        //
    }

    public function update(Request $request, Country $country)
    {
        //
    }

    public function destroy(Country $country)
    {
        //
    }
}
