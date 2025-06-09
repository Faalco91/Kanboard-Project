<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory; 
    // fillable permet de définir les champs qui peuvent être remplis en masse et ainsi protéger les autres champs de la table 
    protected $fillable = ['name', 'description', 'user_id'];

    public function user()
    {
        // belongsTo est une méthode qui permet de liée un projet à un user 
        // On peut accéder à l'utilisateur propriétaire d'un projet via $project->user
        return $this->belongsTo(User::class);
    }
    
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')
                    ->withPivot('role', 'status', 'invitation_sent_at', 'invitation_accepted_at')
                    ->withTimestamps();
    }
    
    public function pendingMembers()
    {
        return $this->members()->wherePivot('status', 'pending');
    }

    public function acceptedMembers()
    {
        return $this->members()->wherePivot('status', 'accepted');
    }
}
