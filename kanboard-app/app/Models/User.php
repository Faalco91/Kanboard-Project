<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;



//Ajout de l'interface MustVerifyEmail pour la vérification de l'email
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;



    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    public function ownedProjects() {
        return $this->hasMany(Project::class);
    }

    public function memberProjects()
    {
        return $this->belongsToMany(Project::class, 'project_members')
                    ->withPivot('role', 'status', 'invitation_sent_at', 'invitation_accepted_at')
                    ->wherePivot('status', 'accepted');
    }

    public function projects()
    {
        // Utiliser une sous-requête pour obtenir les IDs des projets
        $projectIds = Project::where('user_id', $this->id)
            ->orWhereHas('members', function ($query) {
                $query->where('user_id', $this->id)
                      ->where('status', 'accepted');
            })
            ->pluck('id');

        // Retourner une nouvelle requête basée sur les IDs
        return Project::whereIn('id', $projectIds);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    

}


