<?php

namespace App\Livewire\Statistics;

use Livewire\Component;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StatisticsDashboard extends Component
{
    public $stats = [];
    public $loading = true;



    public function mount()
    {
        $this->loadStatistics();
    }

    public function loadStatistics()
    {
        $this->loading = true;
        
        $user = Auth::user();
        
        // Statistiques des projets
        $this->stats['projects'] = $this->getProjectStatistics($user);
        
        // Statistiques des tâches
        $this->stats['tasks'] = $this->getTaskStatistics($user);
        
        // Statistiques de productivité
        $this->stats['productivity'] = $this->getProductivityStatistics($user);
        

        

        
        $this->loading = false;
    }



    private function getProjectStatistics($user)
    {
        $ownedProjects = Project::where('user_id', $user->id)->count();
        $memberProjects = $user->projects()->where('status', 'accepted')->count();
        $totalProjects = $ownedProjects + $memberProjects;
        
        $activeProjects = Project::where(function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhereHas('members', function($q) use ($user) {
                      $q->where('user_id', $user->id)->where('status', 'accepted');
                  });
        })->whereHas('tasks', function($query) {
            $query->where('updated_at', '>=', now()->subDays(30));
        })->count();

        return [
            'total' => $totalProjects,
            'owned' => $ownedProjects,
            'member' => $memberProjects,
            'active' => $activeProjects,
        ];
    }

    private function getTaskStatistics($user)
    {
        $createdTasks = Task::where('user_id', $user->id)->count();
        
        $assignedTasks = Task::whereHas('project', function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhereHas('members', function($q) use ($user) {
                      $q->where('user_id', $user->id)->where('status', 'accepted');
                  });
        })->count();
        
        $completedTasks = Task::whereHas('project', function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhereHas('members', function($q) use ($user) {
                      $q->where('user_id', $user->id)->where('status', 'accepted');
                  });
        })->whereNotNull('completed_at')->count();
        
        $overdueTasks = Task::whereHas('project', function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhereHas('members', function($q) use ($user) {
                      $q->where('user_id', $user->id)->where('status', 'accepted');
                  });
        })->where('due_date', '<', now())
          ->whereNull('completed_at')
          ->count();

        return [
            'total_created' => $createdTasks,
            'total_assigned' => $assignedTasks,
            'completed' => $completedTasks,
            'overdue' => $overdueTasks,
            'completion_rate' => $assignedTasks > 0 ? round(($completedTasks / $assignedTasks) * 100, 2) : 0,
        ];
    }

    private function getProductivityStatistics($user)
    {
        $completedThisWeek = Task::whereHas('project', function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhereHas('members', function($q) use ($user) {
                      $q->where('user_id', $user->id)->where('status', 'accepted');
                  });
        })->whereNotNull('completed_at')
          ->where('completed_at', '>=', now()->startOfWeek())
          ->count();
        
        $completedThisMonth = Task::whereHas('project', function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhereHas('members', function($q) use ($user) {
                      $q->where('user_id', $user->id)->where('status', 'accepted');
                  });
        })->whereNotNull('completed_at')
          ->where('completed_at', '>=', now()->startOfMonth())
          ->count();
        
        $createdThisWeek = Task::where('user_id', $user->id)
            ->where('created_at', '>=', now()->startOfWeek())
            ->count();

        return [
            'completed_this_week' => $completedThisWeek,
            'completed_this_month' => $completedThisMonth,
            'created_this_week' => $createdThisWeek,
            'weekly_average' => $this->calculateWeeklyAverage($user),
        ];
    }





    private function calculateWeeklyAverage($user)
    {
        $weeks = 4;
        $totalCompleted = 0;
        
        for ($i = 0; $i < $weeks; $i++) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();
            
            $completed = Task::whereHas('project', function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhereHas('members', function($q) use ($user) {
                          $q->where('user_id', $user->id)->where('status', 'accepted');
                      });
            })->whereNotNull('completed_at')
              ->whereBetween('completed_at', [$weekStart, $weekEnd])
              ->count();
            
            $totalCompleted += $completed;
        }
        
        return $weeks > 0 ? round($totalCompleted / $weeks, 2) : 0;
    }

    public function render()
    {
        return view('livewire.statistics.statistics-dashboard');
    }
} 