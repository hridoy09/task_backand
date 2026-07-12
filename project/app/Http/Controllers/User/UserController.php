<?php

namespace App\Http\Controllers\User;

use App\Helpers\SystemHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function profileComplete()
    {
        $title = __('Profile Complete');

        $user = auth()->user();

        return theme('user.profile_data', compact('title', 'user'));
    }

    public function saveCompleteProfile(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'username'     => 'required|string|unique:users',
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'country'      => 'required|string|max:2',
            'phone_number' => 'required|string|max:20',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();

        $user->update($validatedData);

        $user->pc = 1;
        $user->save();

        return to_route('user.dashboard')->withSuccess(__('Profile completed successfully.'));
    }

    public function dashboard()
    {
        $title = 'User Dashboard';

        return theme('user.dashboard', compact('title'));
    }

    public function profile()
    {
        $title = 'Profile';

        $user = auth()->user();

        return theme('user.setting.profile', compact('title', 'user'));
    }

    public function saveProfile(Request $request)
    {
        $validated = $request->validate([
            'first_name'   => 'required|max:' . intval(255 / 2),
            'last_name'    => 'required|max:' . intval(255 / 2),
            // 'username'     => 'required|max:255',
            'city'         => 'nullable|max:255',
            'zipcode'      => 'nullable|max:255',
            'address'      => 'nullable|string',
            'country_code' => 'required|in:' . collect(countries())->pluck('code')->implode(',')
        ]);

        $user = auth()->user();
        $user->fill($validated);
        $user->name = $request->first_name . ' ' . $request->last_name;

        $changedFields = array_keys($user->getDirty());

        $user->save();

        if ($user->email && !empty($changedFields)) {
            $labels = [
                'first_name' => __('First Name'),
                'last_name' => __('Last Name'),
                'city' => __('City'),
                'zipcode' => __('Zip Code'),
                'address' => __('Address'),
                'country_code' => __('Country'),
            ];

            $updatedFields = collect($changedFields)
                ->map(fn ($field) => $labels[$field] ?? Str::title(str_replace('_', ' ', $field)))
                ->implode(', ');

            sendTemplatedNotification(
                $user->email,
                'PROFILE_UPDATED',
                [
                    'user_name' => $user->name,
                    'updated_at' => now()->toDayDateTimeString(),
                    'profile_url' => route('user.setting.profile'),
                    'updated_fields' => $updatedFields,
                ]
            );
        }

        return back()->withSuccess(__('Profile updated successfully'));
    }
}
