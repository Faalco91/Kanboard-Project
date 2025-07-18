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
     * Vérifier si un utilisateur est membre du projet
     */
    public function hasMember(User $user): bool
    {
        // Vérifier si l'utilisateur est le propriétaire du projet
        if ($this->user_id === $user->id) {
            return true;
        }

        // Vérifier si l'utilisateur est membre avec statut accepté
        return $this->members()
                    ->where('user_id', $user->id)
                    ->where('status', 'accepted')
                    ->exists();
    }

    /**
     * Vérifier si un utilisateur peut gérer le projet
     */
    public function canManage(User $user): bool
    {
        // Le propriétaire peut toujours gérer
        if ($this->user_id === $user->id) {
            return true;
        }

        // Les administrateurs peuvent gérer
        return $this->members()
                    ->where('user_id', $user->id)
                    ->where('status', 'accepted')
                    ->whereIn('role', ['admin', 'owner'])
                    ->exists();
    }
}
