<?php

namespace App\Http\Controllers\api\Admin;

use App\Models\Country;
use App\Models\Zippopotam;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Country as CountryResource;

class CountryListController extends Controller
{
    public $model;
    public $mask;

    public function __construct( Country $country, Zippopotam $mask )
    {
        $this->model = $country;
        $this->mask = $mask;
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

    public function mask(Request $request)
    {
        $masks = $this->mask->getCountryZipCodeMask($request);
        return response()->json([
            'success' => true,
            'data' => $masks
        ]);
    }
}
