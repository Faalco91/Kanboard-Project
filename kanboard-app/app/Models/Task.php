<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'color',
        'column',
        'project_id',
        'user_id',
        'due_date',
        'priority',
        'completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    protected $attributes = [
        'priority' => 'medium',
        'column' => 'To Do',
    ];

    // Relations
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accesseurs/Mutateurs pour la priorité
    public function getPriorityAttribute($value)
    {
        return $value ?? 'medium';
    }

    public function setPriorityAttribute($value)
    {
        $this->attributes['priority'] = $value ?: 'medium';
    }
}
