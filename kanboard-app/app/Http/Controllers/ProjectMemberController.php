<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProjectMemberController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;
    
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function index(Project $project)
    {
        $this->authorize('view', $project);

        return view('projects.members.index', [
            'project' => $project->load(['members' => function($query) {
                $query->withPivot('role', 'status', 'invitation_sent_at', 'invitation_accepted_at');
            }])
        ]);
    }

    public function invite(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'required|in:member,admin'
        ]);

        try {
            Log::info('Tentative d\'invitation d\'un membre', [
                'project_id' => $project->id,
                'email' => $validated['email'],
                'role' => $validated['role']
            ]);

            $this->projectService->inviteMember($project, $validated['email'], $validated['role']);
            
            return redirect()->route('project.members.index', $project)
                           ->with('success', 'Invitation envoyée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'invitation d\'un membre', [
                'project_id' => $project->id,
                'email' => $validated['email'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Impossible d\'envoyer l\'invitation : ' . $e->getMessage());
        }
    }

    public function accept(Project $project, User $user)
    {
        // Vérifier si l'utilisateur actuel est celui qui accepte l'invitation
        if (auth()->id() !== $user->id) {
            abort(403);
        }

        try {
            $this->projectService->acceptInvitation($project, $user);
            return redirect()->route('projects.show', $project)
                           ->with('success', 'Invitation acceptée.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible d\'accepter l\'invitation.');
        }
    }

    public function decline(Project $project, User $user)
    {
        // Vérifier si l'utilisateur actuel est celui qui refuse l'invitation
        if (auth()->id() !== $user->id) {
            abort(403);
        }

        try {
            $this->projectService->declineInvitation($project, $user);
            return redirect()->route('dashboard')
                           ->with('success', 'Invitation refusée.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de refuser l\'invitation.');
        }
    }

    public function remove(Project $project, User $user)
    {
        $this->authorize('update', $project);

        // Empêcher la suppression du propriétaire du projet
        $memberRole = $project->members()->where('user_id', $user->id)->value('role');
        if ($memberRole === 'owner') {
            return back()->with('error', 'Impossible de retirer le propriétaire du projet.');
        }

        try {
            $this->projectService->removeMember($project, $user);
            return back()->with('success', 'Membre retiré du projet.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de retirer le membre.');
        }
    }

    public function showInvitation(Project $project)
    {
        // Vérifier si l'utilisateur est connecté
        if (!auth()->check()) {
            // Stocker l'URL de l'invitation dans la session
            session()->put('url.intended', request()->fullUrl());
            return redirect()->route('login')
                           ->with('info', 'Veuillez vous connecter pour voir l\'invitation.');
        }

        // Vérifier si l'utilisateur peut voir l'invitation
        if (!auth()->user()->can('viewInvitation', $project)) {
            return redirect()->route('dashboard')
                           ->with('error', 'Cette invitation n\'est pas valide ou a déjà été traitée.');
        }

        $member = $project->members()
            ->where('user_id', auth()->id())
            ->first();

        return view('projects.invitation', [
            'project' => $project,
            'member' => $member
        ]);
    }

    public function acceptInvitation(Project $project)
    {
        // Vérifier si l'utilisateur peut voir l'invitation
        if (!auth()->user()->can('viewInvitation', $project)) {
            return redirect()->route('dashboard')
                           ->with('error', 'Cette invitation n\'est pas valide ou a déjà été traitée.');
        }

        try {
            DB::transaction(function () use ($project) {
                // Mettre à jour le statut du membre
                $project->members()
                    ->where('user_id', auth()->id())
                    ->update([
                        'status' => 'accepted',
                        'invitation_accepted_at' => now()
                    ]);

                Log::info('Invitation acceptée', [
                    'user_id' => auth()->id(),
                    'project_id' => $project->id
                ]);
            });

            return redirect()->route('projects.show', $project)
                           ->with('success', 'Invitation acceptée avec succès !');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'acceptation de l\'invitation', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'project_id' => $project->id
            ]);

            return redirect()->route('dashboard')
                           ->with('error', 'Une erreur est survenue lors de l\'acceptation de l\'invitation.');
        }
    }

    public function rejectInvitation(Project $project)
    {
        // Vérifier si l'utilisateur peut voir l'invitation
        if (!auth()->user()->can('viewInvitation', $project)) {
            return redirect()->route('dashboard')
                           ->with('error', 'Cette invitation n\'est pas valide ou a déjà été traitée.');
        }

        try {
            DB::transaction(function () use ($project) {
                // Supprimer le membre du projet
                $project->members()
                    ->where('user_id', auth()->id())
                    ->delete();

                Log::info('Invitation rejetée', [
                    'user_id' => auth()->id(),
                    'project_id' => $project->id
                ]);
            });

            return redirect()->route('dashboard')
                           ->with('success', 'Invitation rejetée.');
        } catch (\Exception $e) {
            Log::error('Erreur lors du rejet de l\'invitation', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'project_id' => $project->id
            ]);

            return redirect()->route('dashboard')
                           ->with('error', 'Une erreur est survenue lors du rejet de l\'invitation.');
        }
    }
} 