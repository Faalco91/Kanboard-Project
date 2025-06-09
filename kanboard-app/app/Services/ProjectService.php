<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectInvitation;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class ProjectService
{
    /**
     * Créer un nouveau projet
     */
    public function create(array $data, User $owner): Project
    {
        return DB::transaction(function () use ($data, $owner) {
            $project = Project::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'user_id' => $owner->id,
            ]);

            // Ajouter le créateur comme membre propriétaire
            $project->members()->attach($owner->id, [
                'role' => 'owner',
                'status' => 'accepted',
                'invitation_accepted_at' => now(),
            ]);

            return $project;
        });
    }

    /**
     * Mettre à jour un projet
     */
    public function update(Project $project, array $data): Project
    {
        $project->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? $project->description,
        ]);

        return $project;
    }

    /**
     * Inviter un membre dans le projet
     */
    public function inviteMember(Project $project, string $email, string $role = 'member'): void
    {
        Log::info('Début de l\'invitation', [
            'project_id' => $project->id,
            'email' => $email,
            'role' => $role
        ]);

        try {
            $user = User::where('email', $email)->firstOrFail();
            Log::info('Utilisateur trouvé', ['user_id' => $user->id]);

            if (!$project->members()->where('user_id', $user->id)->exists()) {
                Log::info('Ajout du membre au projet');
                
                DB::transaction(function () use ($project, $user, $role) {
                    $project->members()->attach($user->id, [
                        'role' => $role,
                        'status' => 'pending',
                        'invitation_sent_at' => now(),
                    ]);

                    try {
                        $user->notify(new ProjectInvitation($project, $role));
                        Log::info('Notification envoyée avec succès');
                    } catch (\Exception $e) {
                        Log::error('Erreur lors de l\'envoi de la notification', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        throw $e;
                    }
                });
            } else {
                Log::info('L\'utilisateur est déjà membre du projet');
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'invitation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Accepter une invitation
     */
    public function acceptInvitation(Project $project, User $user): void
    {
        $project->members()->updateExistingPivot($user->id, [
            'status' => 'accepted',
            'invitation_accepted_at' => now(),
        ]);
    }

    /**
     * Refuser une invitation
     */
    public function declineInvitation(Project $project, User $user): void
    {
        $project->members()->updateExistingPivot($user->id, [
            'status' => 'declined',
        ]);
    }

    /**
     * Retirer un membre du projet
     */
    public function removeMember(Project $project, User $user): void
    {
        $project->members()->detach($user->id);
    }

    /**
     * Obtenir tous les projets d'un utilisateur (où il est membre)
     */
    public function getUserProjects(User $user): Collection
    {
        return Project::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('status', 'accepted');
        })->get();
    }
} 