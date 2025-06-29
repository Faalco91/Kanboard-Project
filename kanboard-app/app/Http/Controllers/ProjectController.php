<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\ProjectService;
use App\Services\ICalendarService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectController extends Controller
{
    use AuthorizesRequests;
    
    protected $projectService;
    protected $iCalendarService;

    public function __construct(ProjectService $projectService, ICalendarService $iCalendarService)
    {
        $this->projectService = $projectService;
        $this->iCalendarService = $iCalendarService;
    }

    // Affiche tous les projets de l'utilisateur qui est connecté
    public function index()
    {
        $projects = $this->projectService->getUserProjects(Auth::user());
        return view('projects.index', compact('projects'));
    }

    // Afficher le formulaire de création
    public function create()
    {
        return view('projects.create');
    }

    // Créer un nouveau projet
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = $this->projectService->create($validated, Auth::user());

        if ($request->wantsJson()) {
            return response()->json($project, 201);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projet créé avec succès.');
    }

    // Afficher un seul projet (celui sélectionné)
    public function show($id)
    {
        $project = Project::findOrFail($id);
        $userInitials = strtoupper(substr(auth()->user()->name, 0, 1));
    
        $this->authorize('view', $project);
    
        // Récupère toutes les tâches liées à ce projet
        $tasks = $project->tasks()->get();
    
        return view('projects.show', compact('project', 'tasks', 'userInitials'));
    }

    // Mettre à jour un projet
    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = $this->projectService->update($project, $validated);

        if ($request->wantsJson()) {
            return response()->json($project);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projet mis à jour avec succès.');
    }

    // Supprimer un projet
    public function destroy(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $this->authorize('delete', $project);

        $project->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Projet supprimé']);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Projet supprimé avec succès.');
    }

    // Exporter le projet au format iCalendar
    public function exportICalendar($id)
    {
        $project = Project::findOrFail($id);
        $this->authorize('view', $project);

        $icalContent = $this->iCalendarService->generateICalendar($project);
        
        $filename = 'kanboard-' . $project->name . '-' . now()->format('Y-m-d') . '.ics';
        
        return response($icalContent)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
