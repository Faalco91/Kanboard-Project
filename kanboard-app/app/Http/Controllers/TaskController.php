<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use App\Models\Column;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller 
{
    public function store(Request $request) 
    {
        // Validation des données
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'column' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'due_date' => 'nullable|date',
        ]);

        // Vérifier que l'utilisateur a accès au projet
        $project = Project::findOrFail($validated['project_id']);
        if (!$project->hasMember(auth()->user())) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        // Créer la tâche
        $task = Task::create([
            'title' => $validated['title'],
            'category' => $validated['category'] ?? null,
            'color' => $validated['color'] ?? null,
            'column' => $validated['column'],
            'project_id' => $validated['project_id'],
            'user_id' => auth()->id(),
            'due_date' => $validated['due_date'] ?? null,
        ]);

        // Charger les relations
        $task->load('user', 'project');

        return response()->json([
            'success' => true,
            'task' => $task,
            'message' => 'Tâche créée avec succès'
        ]);
    }

    public function update(Request $request, Task $task)
    {
        // Vérifier les permissions
        $project = $task->project;
        if (!$project->hasMember(auth()->user())) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'category' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'column' => 'sometimes|string|max:255',
            'due_date' => 'nullable|date',
        ]);

        // Mettre à jour la tâche
        $task->update($validated);

        return response()->json([
            'success' => true,
            'task' => $task->load('user', 'project'),
            'message' => 'Tâche mise à jour avec succès'
        ]);
    }

    public function destroy(Task $task)
    {
        // Vérifier les permissions
        $project = $task->project;
        if (!$project->hasMember(auth()->user())) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tâche supprimée avec succès'
        ]);
    }

    public function updateStatus(Request $request, Task $task)
    {
        // Vérifier les permissions
        $project = $task->project;
        if (!$project->hasMember(auth()->user())) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $validated = $request->validate([
            'column' => 'required|string|max:255',
        ]);

        $task->update(['column' => $validated['column']]);

        return response()->json([
            'success' => true,
            'task' => $task,
            'message' => 'Statut mis à jour'
        ]);
    }
}