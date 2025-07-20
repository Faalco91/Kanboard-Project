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
    public $selectedPeriod = 'week';
    public $selectedProject = null;
    public $projects = [];

    protected $listeners = ['refreshStats' => 'loadStatistics'];

    public function mount()
    {
        $this->loadStatistics();
        $this->loadProjects();
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
        
        // Statistiques par période
        $this->stats['period'] = $this->getPeriodStatistics($user);
        
        // Statistiques détaillées du projet sélectionné
        if ($this->selectedProject) {
            $project = Project::find($this->selectedProject);
            if ($project && $project->hasMember($user)) {
                $this->stats['project_details'] = $this->getProjectDetails($project);
            }
        }
        
        $this->loading = false;
    }

    public function loadProjects()
    {
        $user = Auth::user();
        $this->projects = Project::where(function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhereHas('members', function($q) use ($user) {
                      $q->where('user_id', $user->id)->where('status', 'accepted');
                  });
        })->get(['id', 'name']);
    }

    public function updatedSelectedPeriod()
    {
        $this->loadStatistics();
    }

    public function updatedSelectedProject()
    {
        $this->loadStatistics();
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

    private function getPeriodStatistics($user)
    {
        $period = $this->selectedPeriod;
        $startDate = null;
        $endDate = now();
        
        switch ($period) {
            case 'week':
                $startDate = now()->startOfWeek();
                break;
            case 'month':
                $startDate = now()->startOfMonth();
                break;
            case 'quarter':
                $startDate = now()->startOfQuarter();
                break;
            case 'year':
                $startDate = now()->startOfYear();
                break;
        }
        
        $tasksCreated = Task::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
            
        $tasksCompleted = Task::whereHas('project', function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhereHas('members', function($q) use ($user) {
                      $q->where('user_id', $user->id)->where('status', 'accepted');
                  });
        })->whereNotNull('completed_at')
          ->whereBetween('completed_at', [$startDate, $endDate])
          ->count();
          
        $projectsCreated = Project::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        return [
            'tasks_created' => $tasksCreated,
            'tasks_completed' => $tasksCompleted,
            'projects_created' => $projectsCreated,
            'period' => $period,
        ];
    }

    private function getProjectDetails($project)
    {
        $totalTasks = $project->tasks()->count();
        $completedTasks = $project->tasks()->whereNotNull('completed_at')->count();
        $pendingTasks = $project->tasks()->whereNull('completed_at')->count();
        $overdueTasks = $project->tasks()
            ->where('due_date', '<', now())
            ->whereNull('completed_at')
            ->count();
            
        $columns = $project->tasks()
            ->selectRaw('column, count(*) as count')
            ->groupBy('column')
            ->get()
            ->pluck('count', 'column')
            ->toArray();

        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'pending_tasks' => $pendingTasks,
            'overdue_tasks' => $overdueTasks,
            'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0,
            'columns' => $columns,
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