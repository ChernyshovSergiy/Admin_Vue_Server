<?php

namespace App\Http\Controllers\api\Orders;


use App\Http\Requests\Order\Modeling\ValidateRequest;
use App\Models\Modeling;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;

class ModelingOrderController extends Controller
{
    public $model;

    public function __construct(Modeling $modeling)
    {
        $this->model = $modeling;
    }

    public function store(ValidateRequest $request)
    {
        $this->model::addNewModelingOrder($request);
        return response()->json([
            'massage' => 'Order for Modeling delivered'
        ]);
    }

    public function verify($token)
    {
        $this->model->verifyModelingOrder($token);

        return response()->json([
            'status' => Lang::get('mail.your_order_is_confirmed')
        ]);
    }
}
