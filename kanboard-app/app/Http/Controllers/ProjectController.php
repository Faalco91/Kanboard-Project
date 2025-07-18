<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Récupérer les projets où l'utilisateur est propriétaire ou membre
        $projects = Project::where('user_id', $user->id)
            ->orWhereHas('members', function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->where('status', 'accepted');
            })
            ->withCount(['tasks', 'members'])
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

        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projet créé avec succès !');
    }

    public function show(Project $project)
    {
        // Vérifier que l'utilisateur a accès au projet
        if (!$project->hasMember(Auth::user())) {
            abort(403, 'Accès non autorisé à ce projet.');
        }

        // Récupérer les tâches du projet
        $tasks = $project->tasks()->with('user')->get();

        return view('projects.show', compact('project', 'tasks'));
    }

    public function edit(Project $project)
    {
        // Seul le propriétaire peut modifier
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Seul le propriétaire peut modifier ce projet.');
        }

        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        // Seul le propriétaire peut modifier
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Seul le propriétaire peut modifier ce projet.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projet mis à jour avec succès !');
    }

    public function destroy(Project $project)
    {
        // Seul le propriétaire peut supprimer
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Seul le propriétaire peut supprimer ce projet.');
        }

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Projet supprimé avec succès !');
    }

    /**
     * Vue Liste des tâches (CORRIGÉE)
     */
    public function listView(Project $project)
    {
        // Vérifier l'accès
        if (!$project->hasMember(Auth::user())) {
            abort(403, 'Accès non autorisé à ce projet.');
        }

        // Récupérer les tâches avec filtres et recherche
        $query = $project->tasks()->with('user');

        // Filtres par statut
        if (request('status')) {
            $query->where('column', request('status'));
        }

        // Filtres par catégorie
        if (request('category')) {
            $query->where('category', request('category'));
        }

        // Recherche par titre
        if (request('search')) {
            $query->where('title', 'like', '%' . request('search') . '%');
        }

        // Tri
        $sortBy = request('sort', 'created_at');
        $sortOrder = request('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // CORRECTION: Utiliser paginate au lieu de get
        $tasks = $query->paginate(20);

        // Récupérer les catégories et statuts pour les filtres
        $categories = $project->tasks()->distinct()->whereNotNull('category')->pluck('category');
        $statuses = ['Backlog', 'To Do', 'In Progress', 'To Be Checked', 'Done'];

        return view('projects.list', compact('project', 'tasks', 'categories', 'statuses'));
    }

    /**
     * Vue Calendrier des tâches (CORRIGÉE)
     */
    public function calendarView(Project $project)
    {
        // Vérifier l'accès
        if (!$project->hasMember(Auth::user())) {
            abort(403, 'Accès non autorisé à ce projet.');
        }

        // Récupérer les tâches avec dates d'échéance
        $tasks = $project->tasks()
            ->with('user')
            ->whereNotNull('due_date')
            ->get();

        // Préparer les données pour le calendrier
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
     * Statistiques du projet
     */
    public function stats(Project $project)
    {
        // Vérifier l'accès
        if (!$project->hasMember(Auth::user())) {
            abort(403, 'Accès non autorisé à ce projet.');
        }

        $stats = [
            'total_tasks' => $project->tasks()->count(),
            'completed_tasks' => $project->tasks()->where('column', 'Done')->count(),
            'in_progress_tasks' => $project->tasks()->where('column', 'In Progress')->count(),
            'overdue_tasks' => $project->tasks()
                ->whereNotNull('due_date')
                ->where('due_date', '<', now())
                ->where('column', '!=', 'Done')
                ->count(),
            
            // Répartition par colonne
            'tasks_by_column' => $project->tasks()
                ->groupBy('column')
                ->selectRaw('column, count(*) as count')
                ->pluck('count', 'column'),
            
            // Répartition par catégorie
            'tasks_by_category' => $project->tasks()
                ->whereNotNull('category')
                ->groupBy('category')
                ->selectRaw('category, count(*) as count')
                ->pluck('count', 'category'),
            
            // Répartition par utilisateur
            'tasks_by_user' => $project->tasks()
                ->with('user')
                ->get()
                ->groupBy('user.name')
                ->map->count(),
        ];

        return view('projects.stats', compact('project', 'stats'));
    }

    /**
     * Export iCal
     */
    public function exportIcal(Project $project)
    {
        // Vérifier l'accès
        if (!$project->hasMember(Auth::user())) {
            abort(403, 'Accès non autorisé à ce projet.');
        }

        $tasks = $project->tasks()
            ->whereNotNull('due_date')
            ->get();

        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//Kanboard//NONSGML v1.0//EN\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";

        foreach ($tasks as $task) {
            $ical .= "BEGIN:VEVENT\r\n";
            $ical .= "UID:" . $task->id . "@kanboard.local\r\n";
            $ical .= "DTSTAMP:" . now()->format('Ymd\THis\Z') . "\r\n";
            
            $dueDate = $task->due_date instanceof \Carbon\Carbon ? $task->due_date : \Carbon\Carbon::parse($task->due_date);
            $ical .= "DTSTART;VALUE=DATE:" . $dueDate->format('Ymd') . "\r\n";
            
            $ical .= "SUMMARY:" . str_replace(',', '\,', $task->title) . "\r\n";
            
            if ($task->description) {
                $ical .= "DESCRIPTION:" . str_replace(',', '\,', $task->description) . "\r\n";
            }
            
            if ($task->category) {
                $ical .= "CATEGORIES:" . $task->category . "\r\n";
            }
            
            $ical .= "STATUS:" . ($task->column === 'Done' ? 'COMPLETED' : 'NEEDS-ACTION') . "\r\n";
            $ical .= "END:VEVENT\r\n";
        }

        $ical .= "END:VCALENDAR\r\n";

        return response($ical)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $project->name . '.ics"');
    }

    /**
     * API pour synchronisation hors-ligne
     */
    public function syncData(Project $project)
    {
        // Vérifier l'accès
        if (!$project->hasMember(Auth::user())) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $data = [
            'project' => $project,
            'tasks' => $project->tasks()->with('user')->get(),
            'members' => $project->members()->get(),
            'last_sync' => now()->toISOString(),
        ];

        return response()->json($data);
    }
}
