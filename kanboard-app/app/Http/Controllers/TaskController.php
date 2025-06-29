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
        // DEBUG: Log de toutes les données reçues
        Log::info('=== TASK CREATION DEBUG ===');
        Log::info('Request method: ' . $request->method());
        Log::info('Request URL: ' . $request->url());
        Log::info('Request headers: ', $request->headers->all());
        Log::info('Request all data: ', $request->all());
        Log::info('Request JSON: ', $request->json()->all());
        Log::info('Raw input: ' . $request->getContent());
        
        // Vérifier si on reçoit les données JSON
        $data = $request->json()->all();
        if (empty($data)) {
            Log::error('Aucune donnée JSON reçue');
            $data = $request->all();
        }
        
        Log::info('Data to validate: ', $data);

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'category' => 'nullable|string|max:255',
                'color' => 'nullable|string|max:7', // Pour couleur hex #ffffff
                'column' => 'required|string|max:255',
                'project_id' => 'required|exists:projects,id',
            ]);
            
            Log::info('Validation passed. Validated data: ', $validated);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed: ', $e->errors());
            Log::error('Validator messages: ', $e->validator->messages()->toArray());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors(),
                'received_data' => $request->all()
            ], 422);
        }

        // Vérifier que l'utilisateur a accès au projet
        $project = Project::findOrFail($validated['project_id']);
        if (!$project->hasMember(auth()->user())) {
            Log::error('User unauthorized for project: ' . $validated['project_id']);
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        Log::info('User authorized, creating column...');

        // Trouver ou créer la colonne correspondante
        $column = Column::firstOrCreate([
            'project_id' => $validated['project_id'],
            'name' => $validated['column']
        ], [
            'position' => Column::where('project_id', $validated['project_id'])->max('position') + 1 ?? 1,
            'is_terminal' => in_array($validated['column'], ['Done', 'Fait', 'Terminé', 'Annulé'])
        ]);

        Log::info('Column created/found: ', $column->toArray());

        // Calculer la position dans la colonne
        $maxPosition = Task::where('column_id', $column->id)->max('position') ?? 0;

        $taskData = [
            'title' => $validated['title'],
            'description' => null,
            'category' => $validated['category'] ?? null,
            'project_id' => $validated['project_id'],
            'column_id' => $column->id,
            'user_id' => auth()->id(),
            'position' => $maxPosition + 1,
            'column' => $validated['column'], // Compatibilité
            'color' => $validated['color'] ?? null,
        ];

        Log::info('Creating task with data: ', $taskData);

        $task = Task::create($taskData);

        Log::info('Task created: ', $task->toArray());

        // Charger les relations pour la réponse
        $task->load(['creator', 'column']);

        Log::info('Task with relations: ', $task->toArray());

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

        $request->validate([
            'column' => 'sometimes|string|max:255',
            'title' => 'sometimes|string|max:255',
            'category' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);

        $updateData = [];

        // Si on change de colonne
        if ($request->has('column')) {
            $column = Column::firstOrCreate([
                'project_id' => $task->project_id,
                'name' => $request->column
            ], [
                'position' => Column::where('project_id', $task->project_id)->max('position') + 1 ?? 1,
                'is_terminal' => in_array($request->column, ['Done', 'Fait', 'Terminé', 'Annulé'])
            ]);

            $updateData['column_id'] = $column->id;
            $updateData['column'] = $request->column; // Compatibilité
        }

        // Autres champs
        if ($request->has('title')) $updateData['title'] = $request->title;
        if ($request->has('category')) $updateData['category'] = $request->category;
        if ($request->has('color')) $updateData['color'] = $request->color;

        $task->update($updateData);

        return response()->json([
            'success' => true,
            'task' => $task->load(['creator', 'column']),
            'message' => 'Tâche mise à jour'
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
            'message' => 'Tâche supprimée'
        ]);
    }
}
