<?php

namespace App\Http\Controllers;

use App\Models\UserSkill;
use Illuminate\Http\Request;

class UserSkillController extends Controller
{
    // Get all skills for a user
    public function index($userId)
    {
        return response()->json(UserSkill::where('user_id', $userId)->get());
    }

    // Store a new skill
    public function store(Request $request, $userId)
    {
        $data = $request->validate([
            'skill' => 'required|string|max:255',
        ]);

        $skill = UserSkill::create([
            'user_id' => $userId,
            'skill' => $data['skill'],
        ]);

        return redirect()->back()->with('success', 'Skill added.');
    }

    // Delete a skill
    public function destroy($id)
    {
        UserSkill::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Skill removed.');
    }
}
