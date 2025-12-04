<?php

namespace App\Http\Controllers;

use App\Models\AiSuggestion;
use Illuminate\Http\Request;

class AiSuggestionController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Users can view their own suggestions, admins can manage all
        $this->middleware('auth');
    }

    /**
     * Display a listing of AI suggestions.
     */
    public function index(Request $request)
    {
        $query = AiSuggestion::query();

        // Users see only their own suggestions, admins see all
        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $suggestions = $query->with(['user', 'resume'])->latest()->paginate(20);
        return view('admin.ai-suggestions.index', compact('suggestions'));
    }

    /**
     * Display the specified AI suggestion.
     */
    public function show(AiSuggestion $aiSuggestion)
    {
        // Users can only view their own suggestions
        if (!auth()->user()->isAdmin() && $aiSuggestion->user_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        return view('admin.ai-suggestions.show', compact('aiSuggestion'));
    }

    /**
     * Update the specified AI suggestion status.
     */
    public function update(Request $request, AiSuggestion $aiSuggestion)
    {
        // Users can only update their own suggestions
        if (!auth()->user()->isAdmin() && $aiSuggestion->user_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,dismissed',
        ]);

        $aiSuggestion->update($validated);

        return redirect()->route('admin.ai-suggestions.index')->with('success', 'Suggestion updated successfully.');
    }

    /**
     * Remove the specified AI suggestion.
     */
    public function destroy(AiSuggestion $aiSuggestion)
    {
        // Users can only delete their own suggestions
        if (!auth()->user()->isAdmin() && $aiSuggestion->user_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        $aiSuggestion->delete();

        return redirect()->route('admin.ai-suggestions.index')->with('success', 'Suggestion deleted successfully.');
    }
}

