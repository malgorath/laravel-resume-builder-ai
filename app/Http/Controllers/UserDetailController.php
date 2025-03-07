<?php

namespace App\Http\Controllers;

use App\Models\UserDetail;
use Illuminate\Http\Request;

class UserDetailController extends Controller
{
    // Get user details
    public function show($userId)
    {
        return response()->json(UserDetail::where('user_id', $userId)->first());
    }

    // Edit user details
    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    // Store or update user details
    public function update(Request $request, $userId)
    {
        $data = $request->validate([
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'linkedin' => 'nullable|string',
            'website' => 'nullable|string',
            'github' => 'nullable|string',
            'other_contact' => 'nullable|string',
        ]);

        $userDetail = UserDetail::updateOrCreate(
            ['user_id' => $userId],
            $data
        );

        return response()->json($userDetail);
    }
}
