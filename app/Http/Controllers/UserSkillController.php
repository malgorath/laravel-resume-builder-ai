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

    public function confirmNewSkills(Request $request)
    {
        $user = Auth::user();
        $skills = $request->input('skills', []);

        foreach ($skills as $skillName) {
            // Check if skill exists, otherwise create
            $skill = Skill::firstOrCreate(['name' => $skillName]);

            // Assign skill to user
            UserSkill::firstOrCreate([
                'user_id' => $user->id,
                'skill_id' => $skill->id
            ]);
        }

        return response()->json(['message' => 'Skills updated successfully.']);
    }

}
