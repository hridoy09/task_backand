<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Feed;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function dashboard(Request $request)
    {
        $feeds = Feed::with('user:id,name')
            ->visibleTo(auth()->id())
            ->latest()
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'note'   => 'user_dashbaord',
            'user'   => auth()->user(),
            'feeds'  => $feeds,
        ]);
    }
}
