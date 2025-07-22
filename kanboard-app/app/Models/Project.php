<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'user_id',
    ];

    protected $appends = ['members_count'];

    /**
     * Événements du modèle
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($project) {
            // Vérifier si le propriétaire n'est pas déjà membre
            if (!$project->members()->where('user_id', $project->user_id)->exists()) {
                $project->members()->attach($project->user_id, [
                    'role' => 'owner',
                    'status' => 'accepted',
                    'invitation_accepted_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')
                    ->withPivot('role', 'status', 'invitation_sent_at', 'invitation_accepted_at')
                    ->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // Méthodes pour la gestion des membres
    public function pendingMembers()
    {
        return $this->members()->wherePivot('status', 'pending');
    }

    public function acceptedMembers()
    {
        return $this->members()->wherePivot('status', 'accepted');
    }

    /**
     * Attribut calculé: Nombre total de membres
     */
    public function getMembersCountAttribute(): int
    {
        return $this->acceptedMembers()->count();
    }

    /**
     * Vérifier si un utilisateur est membre du projet (AMÉLIORÉ)
     */
    public function hasMember($user): bool
    {
        if (!$user) {
            return false;
        }

        // Vérifier si l'utilisateur est le propriétaire
        if ($this->user_id === $user->id) {
            return true;
        }

        // Vérifier si l'utilisateur est membre avec statut accepté
        return $this->acceptedMembers()
                    ->where('user_id', $user->id)
                    ->exists();
    }

    /**
     * Vérifier si un utilisateur peut gérer le projet
     */
    public function canManage($user): bool
    {
        if (!$user) {
            return false;
        }

        // Le propriétaire peut toujours gérer
        if ($this->user_id === $user->id) {
            return true;
        }

        // Les administrateurs peuvent gérer
        return $this->acceptedMembers()
                    ->where('user_id', $user->id)
                    ->whereIn('role', ['admin', 'owner'])
                    ->exists();
    }

    /**
     * Vérifier si un utilisateur est le propriétaire
     */
    public function isOwner($user): bool
    {
        return $user && $this->user_id === $user->id;
    }

    /**
     * Obtenir le rôle d'un utilisateur dans le projet
     */
    public function getUserRole($user): ?string
    {
        if (!$user) {
            return null;
        }

        // Propriétaire
        if ($this->user_id === $user->id) {
            return 'owner';
        }

        // Membre
        $member = $this->acceptedMembers()
            ->where('user_id', $user->id)
            ->first();

        return $member ? $member->pivot->role : null;
    }

    /**
     * Vérifier si un utilisateur a une invitation en attente
     */
    public function hasPendingInvitation($user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->pendingMembers()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Obtenir tous les utilisateurs du projet (propriétaire + membres)
     */
    public function getAllUsers()
    {
        $allUsers = collect();

        // Ajouter le propriétaire
        if ($this->user) {
            $allUsers->push([
                'user' => $this->user,
                'role' => 'owner',
                'status' => 'accepted',
                'is_owner' => true,
            ]);
        }

        // Ajouter les membres acceptés (en excluant le propriétaire si déjà dans les membres)
        $acceptedMembers = $this->acceptedMembers()
            ->where('user_id', '!=', $this->user_id)
            ->get();
            
        foreach ($acceptedMembers as $member) {
            $allUsers->push([
                'user' => $member,
                'role' => $member->pivot->role,
                'status' => $member->pivot->status,
                'is_owner' => false,
            ]);
        }

        return $allUsers;
    }
}
