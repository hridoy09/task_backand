<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Services\FileManager;
use Illuminate\Http\Request;

class ProfileCompleteController extends Controller
{
    public function __invoke(Request $request)
    {
        if(auth()->user()->pc) {
            return response()->json([
                'note'   => 'profile_already_completed',
                'status' => 'success',
                'message' => 'Your profile is already completed',
            ]);
        }

        $request->validate([
            'username' => 'required|unique:users',
            'country'  => 'required|in:'.collect(countries())->pluck('code')->join(','),
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user           = auth()->user();
        $user->username = $request->username;
        $user->country  = $request->country;
        $user->pc       = 1;

        if ($request->hasFile('image')) {
            $user->image = FileManager::uploadToAssets(
                $request->file('image'),
                filePath('user'),
                $user->image,
                handleResize('user')
            );
        }

        $user->save();
        
        return response()->json([
            'note'    => 'profile_completed',
            'status'  => 'success',
            'message' => 'Profile completed successfully',
            'user'    => auth()->user()
        ]);
    }
}
