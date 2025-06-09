<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        // Vérifie si l'utilisateur est membre accepté du projet
        return $project->members()
            ->where('user_id', $user->id)
            ->where('status', 'accepted')
            ->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        return $project->members()
            ->where('user_id', $user->id)
            ->whereIn('role', ['owner', 'admin'])
            ->where('status', 'accepted')
            ->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        return $project->members()
            ->where('user_id', $user->id)
            ->where('role', 'owner')
            ->where('status', 'accepted')
            ->exists();
    }

    /**
     * Determine whether the user can manage members.
     */
    public function manageMembers(User $user, Project $project): bool
    {
        return $project->members()
            ->where('user_id', $user->id)
            ->whereIn('role', ['owner', 'admin'])
            ->where('status', 'accepted')
            ->exists();
    }

    public function viewInvitation(User $user, Project $project): bool
    {
        // Vérifie si l'utilisateur a une invitation en attente
        return $project->members()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();
    }
} 