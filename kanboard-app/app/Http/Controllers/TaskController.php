<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller 
{
    public function store(Request $request) 
    {
        Log::info('=== TASK CREATION DEBUG ===');
        Log::info('Request data:', $request->all());
        Log::info('User ID:', ['user_id' => auth()->id()]);
        
        try {
            // Validation complète avec tous les champs
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'category' => 'nullable|string|max:255',
                'color' => 'nullable|string|max:7',
                'column' => 'required|string|max:255',
                'project_id' => 'required|exists:projects,id',
                'due_date' => 'nullable|date',
                'priority' => 'nullable|in:low,medium,high,urgent',
            ]);

            Log::info('Validation passed:', $validated);

            // Vérifier que l'utilisateur a accès au projet
            $project = Project::findOrFail($validated['project_id']);
            Log::info('Project found:', ['project_id' => $project->id, 'name' => $project->name]);
            
            $user = auth()->user();
            Log::info('Current user:', ['user_id' => $user->id, 'name' => $user->name]);

            // Vérifier si l'utilisateur est membre du projet
            if (!$project->hasMember($user)) {
                Log::warning('User not authorized for project', [
                    'user_id' => $user->id,
                    'project_id' => $project->id
                ]);
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            Log::info('User authorized, creating task...');

            //Créer la tâche avec TOUS les champs
            $task = Task::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'category' => $validated['category'] ?? null,
                'color' => $validated['color'] ?? null,
                'column' => $validated['column'],
                'project_id' => $validated['project_id'],
                'user_id' => $user->id,
                'due_date' => $validated['due_date'] ?? null,
                'priority' => $validated['priority'] ?? 'medium',
            ]);

            Log::info('Task created:', $task->toArray());

            // Charger les relations
            $task->load('user', 'project');

            Log::info('Task with relations loaded');

            return response()->json($task);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error:', $e->errors());
            return response()->json([
                'error' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Task creation error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Task $task)
    {
        try {
            // Vérifier les permissions
            $project = $task->project;
            if (!$project->hasMember(auth()->user())) {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            // Validation complète avec tous les champs modifiables
            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'description' => 'nullable|string|max:1000',
                'category' => 'nullable|string|max:255',
                'color' => 'nullable|string|max:7',
                'column' => 'sometimes|string|max:255',
                'priority' => 'nullable|in:low,medium,high,urgent',
                'due_date' => 'nullable|date',
                'position' => 'sometimes|integer|min:0'
            ]);

            Log::info('Task update data:', [
                'task_id' => $task->id,
                'validated_data' => $validated
            ]);

            // Mettre à jour la tâche avec tous les champs
            $task->update($validated);

            Log::info('Task updated successfully:', $task->toArray());

            return response()->json($task->load('user', 'project'));

        } catch (\Exception $e) {
            Log::error('Task update error:', [
                'error' => $e->getMessage(),
                'task_id' => $task->id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Erreur lors de la mise à jour'
            ], 500);
        }
    }

    public function destroy(Task $task)
    {
        try {
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

        } catch (\Exception $e) {
            Log::error('Task deletion error:', [
                'error' => $e->getMessage(),
                'task_id' => $task->id
            ]);
            return response()->json([
                'error' => 'Erreur lors de la suppression'
            ], 500);
        }
    }

    public function updateStatus(Request $request, Task $task)
    {
        try {
            // Vérifier les permissions
            $project = $task->project;
            if (!$project->hasMember(auth()->user())) {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            $validated = $request->validate([
                'column' => 'required|string|max:255',
            ]);

            $task->update(['column' => $validated['column']]);

            return response()->json($task);

        } catch (\Exception $e) {
            Log::error('Task status update error:', [
                'error' => $e->getMessage(),
                'task_id' => $task->id
            ]);
            return response()->json([
                'error' => 'Erreur lors de la mise à jour du statut'
            ], 500);
        }
    }

    public function show(Task $task)
    {
        // Vérifier les permissions
        $project = $task->project;
        if (!$project->hasMember(auth()->user())) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        // Charger les relations
        $task->load('user', 'project');

        return response()->json([
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'category' => $task->category,
            'color' => $task->color,
            'column' => $task->column,
            'due_date' => $task->due_date ? $task->due_date->format('Y-m-d') : null,
            'priority' => $task->priority,
            'user' => $task->user ? [
                'id' => $task->user->id,
                'name' => $task->user->name
            ] : null,
            'created_at' => $task->created_at,
            'updated_at' => $task->updated_at
        ]);
    }

    public function getData(Task $task)
    {
        // Vérifier les permissions
        if (!$task->project->hasMember(auth()->user())) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }
        
        return response()->json([
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'category' => $task->category,
            'color' => $task->color,
            'column' => $task->column,
            'due_date' => $task->due_date ? $task->due_date->format('Y-m-d') : null,
            'priority' => $task->priority ?? 'medium',
            'user' => $task->user ? [
                'id' => $task->user->id,
                'name' => $task->user->name
            ] : null,
            'created_at' => $task->created_at,
            'updated_at' => $task->updated_at
        ]);
    }
}
