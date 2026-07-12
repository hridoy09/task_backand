<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function dashboard()
    {
        return response()->json([
            'status' => 'success',
            'note'   => 'user_dashbaord',
            'user'   => auth()->user()
        ]);
    }
}
