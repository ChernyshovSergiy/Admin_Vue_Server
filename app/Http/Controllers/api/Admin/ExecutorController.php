<?php

namespace App\Http\Controllers\api\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\User as UserResource;

class ExecutorController extends Controller
{
    public $model;

    public function __construct( User $executor )
    {
        $this->model = $executor;
    }
    public function index($status): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return UserResource::collection($this->model->getExecutorNameByStatus($status));
    }

    public function store(Request $request)
    {
        //
    }

    public function show(User $user)
    {
        //
    }

    public function update(Request $request, User $user)
    {
        //
    }

    public function destroy(User $user)
    {
        //
    }
}
