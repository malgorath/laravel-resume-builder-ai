<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use Illuminate\Http\Request;

class PromptController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Prompt::class);

        $prompts = Prompt::orderBy('title')->paginate(15);

        return view('admin.prompts.index', compact('prompts'));
    }

    public function create()
    {
        $this->authorize('create', Prompt::class);

        return view('admin.prompts.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Prompt::class);

        $data = $this->validatedData($request);

        Prompt::create($data);

        return redirect()->route('admin.prompts.index')->with('success', 'Prompt created.');
    }

    public function edit(Prompt $prompt)
    {
        $this->authorize('update', $prompt);

        return view('admin.prompts.edit', compact('prompt'));
    }

    public function update(Request $request, Prompt $prompt)
    {
        $this->authorize('update', $prompt);

        $data = $this->validatedData($request, $prompt->id);

        $prompt->update($data);

        return redirect()->route('admin.prompts.index')->with('success', 'Prompt updated.');
    }

    public function destroy(Prompt $prompt)
    {
        $this->authorize('delete', $prompt);

        $prompt->delete();

        return redirect()->route('admin.prompts.index')->with('success', 'Prompt deleted.');
    }

    private function validatedData(Request $request, ?int $ignoreId = null): array
    {
        $uniqueRule = 'unique:prompts,key';
        if ($ignoreId) {
            $uniqueRule .= ',' . $ignoreId;
        }

        $validated = $request->validate([
            'key' => ['required', 'string', 'max:191', $uniqueRule],
            'title' => ['required', 'string', 'max:191'],
            'body' => ['required', 'string'],
            'temperature' => ['nullable', 'numeric'],
            'top_p' => ['nullable', 'numeric'],
            'top_k' => ['nullable', 'numeric'],
            'repeat_penalty' => ['nullable', 'numeric'],
            'num_ctx' => ['nullable', 'integer'],
            'seed' => ['nullable', 'integer'],
            'max_tokens' => ['nullable', 'integer'],
        ]);

        $config = [];
        foreach (['temperature', 'top_p', 'top_k', 'repeat_penalty'] as $field) {
            if ($request->filled($field)) {
                $config[$field] = (float) $request->input($field);
            }
        }

        foreach (['num_ctx', 'seed', 'max_tokens'] as $field) {
            if ($request->filled($field)) {
                $config[$field] = (int) $request->input($field);
            }
        }

        $validated['config'] = $config;

        return $validated;
    }
}

