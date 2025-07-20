<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $projects = Project::where('user_id', $user->id)
            ->orWhereHas('members', function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->where('status', 'accepted');
            })
            ->withCount('tasks')
            ->latest()
            ->get();
        
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
    ]);

    try {
        // Créer le projet
        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projet créé avec succès !');
            
    } catch (\Exception $e) {
        Log::error('Erreur lors de la création du projet:', [
            'error' => $e->getMessage(),
            'user_id' => Auth::id(),
            'data' => $validated,
            'trace' => $e->getTraceAsString()
        ]);
        
        return back()
            ->withInput()
            ->with('error', 'Erreur lors de la création du projet: ' . $e->getMessage());
    }
}

    public function show(Project $project)
    {
        if (!$project->hasMember(Auth::user())) {
            abort(403, 'Accès non autorisé à ce projet.');
        }

        $tasks = $project->tasks()->with('user')->get();

        return view('projects.show', compact('project', 'tasks'));
    }

    public function edit(Project $project)
    {
        if (!$project->canManage(Auth::user())) {
            abort(403, 'Vous n\'avez pas les permissions pour modifier ce projet.');
        }

        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        if (!$project->canManage(Auth::user())) {
            abort(403, 'Vous n\'avez pas les permissions pour modifier ce projet.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $project->update($validated);

        return redirect()->route('projects.edit', $project)
            ->with('success', 'Projet mis à jour avec succès !');
    }

    public function destroy(Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Seul le propriétaire peut supprimer ce projet.');
        }

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Projet supprimé avec succès !');
    }

    /**
     * Vue Liste des tâches
     */
    public function listView(Project $project)
    {
        if (!$project->hasMember(Auth::user())) {
            abort(403, 'Accès non autorisé à ce projet.');
        }

        $query = $project->tasks()->with('user');

        // Filtres
        if (request('status')) {
            $query->where('column', request('status'));
        }

        if (request('category')) {
            $query->where('category', request('category'));
        }

        if (request('search')) {
            $query->where('title', 'like', '%' . request('search') . '%');
        }

        // Tri
        $sortBy = request('sort', 'created_at');
        $sortOrder = request('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $tasks = $query->paginate(20);

        $categories = $project->tasks()->distinct()->whereNotNull('category')->pluck('category');
        $statuses = ['Backlog', 'To Do', 'In Progress', 'To Be Checked', 'Done'];

        return view('projects.list', compact('project', 'tasks', 'categories', 'statuses'));
    }

    /**
     * Vue Calendrier des tâches
     */
    public function calendarView(Project $project)
    {
        if (!$project->hasMember(Auth::user())) {
            abort(403, 'Accès non autorisé à ce projet.');
        }

        $tasks = $project->tasks()
            ->with('user')
            ->whereNotNull('due_date')
            ->get();

        $calendarTasks = $tasks->map(function ($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'start' => $task->due_date instanceof \Carbon\Carbon ? $task->due_date->format('Y-m-d') : $task->due_date,
                'backgroundColor' => $task->color ?? '#3b82f6',
                'borderColor' => $task->color ?? '#3b82f6',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'category' => $task->category,
                    'column' => $task->column,
                    'user' => $task->user->name ?? 'Non assigné',
                ],
            ];
        });

        return view('projects.calendar', compact('project', 'calendarTasks'));
    }

    /**
     * Export iCal
     */
    public function exportIcal(Project $project)
    {
        if (!$project->hasMember(Auth::user())) {
            abort(403, 'Accès non autorisé à ce projet.');
        }

        $tasks = $project->tasks()->whereNotNull('due_date')->get();

        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//Kanboard//Kanboard//FR\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";

        foreach ($tasks as $task) {
            $ical .= "BEGIN:VEVENT\r\n";
            $ical .= "UID:" . $task->id . "@kanboard.local\r\n";
            $ical .= "DTSTAMP:" . now()->format('Ymd\THis\Z') . "\r\n";
            $ical .= "DTSTART:" . \Carbon\Carbon::parse($task->due_date)->format('Ymd') . "\r\n";
            $ical .= "SUMMARY:" . $task->title . "\r\n";
            if ($task->description) {
                $ical .= "DESCRIPTION:" . str_replace(["\r\n", "\n", "\r"], "\\n", $task->description) . "\r\n";
            }
            $ical .= "STATUS:" . ($task->column === 'Done' ? 'COMPLETED' : 'CONFIRMED') . "\r\n";
            $ical .= "END:VEVENT\r\n";
        }

        $ical .= "END:VCALENDAR\r\n";

        return response($ical)
            ->header('Content-Type', 'text/calendar')
            ->header('Content-Disposition', 'attachment; filename="' . $project->name . '.ics"');
    }

    /**
     * Statistiques du projet
     */
    public function stats(Project $project)
    {
        if (!$project->hasMember(Auth::user())) {
            abort(403, 'Accès non autorisé à ce projet.');
        }

        $stats = [
            'total_tasks' => $project->tasks()->count(),
            'completed_tasks' => $project->tasks()->where('column', 'Done')->count(),
            'in_progress_tasks' => $project->tasks()->where('column', 'In Progress')->count(),
            'pending_tasks' => $project->tasks()->whereIn('column', ['Backlog', 'To Do'])->count(),
            'overdue_tasks' => $project->tasks()
                ->whereNotNull('due_date')
                ->where('due_date', '<', now())
                ->where('column', '!=', 'Done')
                ->count(),
        ];

        $tasksByCategory = $project->tasks()
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category');

        $tasksByUser = $project->tasks()
            ->join('users', 'tasks.user_id', '=', 'users.id')
            ->selectRaw('users.name, COUNT(*) as count')
            ->groupBy('users.id', 'users.name')
            ->pluck('count', 'name');

        return view('projects.stats', compact('project', 'stats', 'tasksByCategory', 'tasksByUser'));
    }
}
