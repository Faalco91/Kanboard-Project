<?php 

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Task; 

class TaskController extends Controller {

    public function store(Request $request) {
    $request->validate([
        'title' => 'required|string|max:255',
        'category' => 'nullable|string|max:255',
        'color' => 'nullable|string|max:255',
        'column' => 'required|string|max:255',
        'project_id' => 'required|exists:projects,id',
        'due_date' => 'nullable|date',
    ]);

    $task = Task::create([
        'title' => $request->title,
        'category' => $request->category,
        'color' => $request->color,
        'column' => $request->column,
        'project_id' => $request->project_id,
        'user_id' => auth()->id(),
        'due_date' => $request->due_date,
    ]);

    return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'column' => 'nullable|string|max:255',
            'due_date' => 'nullable|date',
        ]);
    
        $task->update($request->only(['title', 'category', 'color', 'column', 'due_date']));
    
        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        // Optionnel : vérifier si l'utilisateur est bien le créateur
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }
    
        $task->delete();
    
        return response()->json(['message' => 'Tâche supprimée']);
    }
    
    
}