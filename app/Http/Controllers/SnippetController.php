<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Snippet;

class SnippetController extends Controller
{
    /**
     * Affiche la liste des snippets, avec filtre optionnel par catégorie.
     */
    public function index(Request $request)
    {
        $query = Snippet::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        return response()->json($query->latest()->get());
    }

    /**
     * Enregistre un nouveau snippet.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'category' => 'required|in:php,html,css,PHP,HTML,CSS',
            'code' => 'required|string',
        ]);

        // Normalize category to uppercase for consistency
        $validated['category'] = strtoupper($validated['category']);

        $snippet = Snippet::create($validated);

        return response()->json([
            'message' => 'Snippet ajouté avec succès',
            'data' => $snippet
        ], 201);
    }

    /**
     * (Optionnel) Affiche un snippet spécifique.
     */
    public function show(string $id)
    {
        $snippet = Snippet::find($id);

        if (!$snippet) {
            return response()->json(['message' => 'Snippet non trouvé'], 404);
        }

        return response()->json($snippet);
    }

    /**
     * (Optionnel) Met à jour un snippet.
     */
    public function update(Request $request, string $id)
    {
        $snippet = Snippet::find($id);

        if (!$snippet) {
            return response()->json(['message' => 'Snippet non trouvé'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:255',
            'category' => 'sometimes|in:php,html,css,PHP,HTML,CSS',
            'code' => 'sometimes|string',
        ]);

        if (isset($validated['category'])) {
            $validated['category'] = strtoupper($validated['category']);
        }

        $snippet->update($validated);

        return response()->json([
            'message' => 'Snippet mis à jour',
            'data' => $snippet
        ]);
    }

    /**
     * (Optionnel) Supprime un snippet.
     */
    public function destroy(string $id)
    {
        $snippet = Snippet::find($id);

        if (!$snippet) {
            return response()->json(['message' => 'Snippet non trouvé'], 404);
        }

        $snippet->delete();

        return response()->json(['message' => 'Snippet supprimé']);
    }
}
