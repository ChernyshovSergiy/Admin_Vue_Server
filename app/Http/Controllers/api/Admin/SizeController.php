<?php

namespace App\Http\Controllers\api\Admin;

use App\Models\Size;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Size as SizeResource;

class SizeController extends Controller
{
    public $model;

    public function __construct( Size $size )
    {
        $this->model = $size;
    }
    public function index($cLang): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return SizeResource::collection($this->model->getActiveSizeByLang($cLang));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Size  $size
     * @return \Illuminate\Http\Response
     */
    public function show(Size $size)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Size  $size
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Size $size)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Size  $size
     * @return \Illuminate\Http\Response
     */
    public function destroy(Size $size)
    {
        //
    }
}
