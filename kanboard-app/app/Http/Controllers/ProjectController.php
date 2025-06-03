<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // Affiche tous les projets de l'utilisateur qui est connecté
    public function index()
    {
        $projects = Auth::user()->projects; // via relation dans User
        return view('projects.index', compact('projects'));
    }
        // Créer un nouveau projet
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => Auth::id(),
        ]);

        return response()->json($project, 201);
    }

    // Afficher un seul projet (celui sélectionné)
    public function show($id)
    {
        $project = Project::findOrFail($id);
        $this->authorize('view', $project);
        return response()->json($project);
    }

    // Mettre à jour un projet
    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $this->authorize('update', $project);

        $project->update($request->only(['name', 'description']));

        return response()->json($project);
    }

    // Supprimer un projet
    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $this->authorize('delete', $project);

        $project->delete();

        return response()->json(['message' => 'Projet supprimé']);
    }
}
