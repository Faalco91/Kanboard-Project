<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    /**
     * Obtenir les statistiques générales pour l'utilisateur connecté
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Statistiques des projets
        $projectStats = $this->getProjectStatistics($user);
        
        // Statistiques des tâches
        $taskStats = $this->getTaskStatistics($user);
        
        // Statistiques de productivité
        $productivityStats = $this->getProductivityStatistics($user);
        
        return response()->json([
            'success' => true,
            'data' => [
                'projects' => $projectStats,
                'tasks' => $taskStats,
                'productivity' => $productivityStats,
                'generated_at' => now()->toISOString(),
            ]
        ]);
    }

    /**
     * Obtenir les statistiques détaillées d'un projet spécifique
     */
    public function projectStatistics(Request $request, Project $project): JsonResponse
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur a accès au projet
        if (!$project->hasMember($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé à ce projet'
            ], 403);
        }
        
        $stats = [
            'project_info' => [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'created_at' => $project->created_at,
            ],
            'task_overview' => $this->getProjectTaskOverview($project),
            'task_progress' => $this->getProjectTaskProgress($project),
            'member_activity' => $this->getProjectMemberActivity($project),
            'timeline' => $this->getProjectTimeline($project),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Obtenir les statistiques des projets
     */
    private function getProjectStatistics(User $user): array
    {
        // Projets créés par l'utilisateur
        $ownedProjects = Project::where('user_id', $user->id)->count();
        
        // Projets où l'utilisateur est membre
        $memberProjects = $user->projects()->where('status', 'accepted')->count();
        
        // Total des projets
        $totalProjects = $ownedProjects + $memberProjects;
        
        // Projets actifs (avec des tâches récentes)
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

    /**
     * Obtenir les statistiques des tâches
     */
    private function getTaskStatistics(User $user): array
    {
        // Tâches créées par l'utilisateur
        $createdTasks = Task::where('user_id', $user->id)->count();
        
        // Tâches assignées à l'utilisateur (dans ses projets)
        $assignedTasks = Task::whereHas('project', function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhereHas('members', function($q) use ($user) {
                      $q->where('user_id', $user->id)->where('status', 'accepted');
                  });
        })->count();
        
        // Tâches complétées
        $completedTasks = Task::whereHas('project', function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhereHas('members', function($q) use ($user) {
                      $q->where('user_id', $user->id)->where('status', 'accepted');
                  });
        })->whereNotNull('completed_at')->count();
        
        // Tâches en retard
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

    /**
     * Obtenir les statistiques de productivité
     */
    private function getProductivityStatistics(User $user): array
    {
        // Tâches complétées cette semaine
        $completedThisWeek = Task::whereHas('project', function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhereHas('members', function($q) use ($user) {
                      $q->where('user_id', $user->id)->where('status', 'accepted');
                  });
        })->whereNotNull('completed_at')
          ->where('completed_at', '>=', now()->startOfWeek())
          ->count();
        
        // Tâches complétées ce mois
        $completedThisMonth = Task::whereHas('project', function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhereHas('members', function($q) use ($user) {
                      $q->where('user_id', $user->id)->where('status', 'accepted');
                  });
        })->whereNotNull('completed_at')
          ->where('completed_at', '>=', now()->startOfMonth())
          ->count();
        
        // Tâches créées cette semaine
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

    /**
     * Obtenir l'aperçu des tâches d'un projet
     */
    private function getProjectTaskOverview(Project $project): array
    {
        $totalTasks = $project->tasks()->count();
        $completedTasks = $project->tasks()->whereNotNull('completed_at')->count();
        $pendingTasks = $project->tasks()->whereNull('completed_at')->count();
        $overdueTasks = $project->tasks()
            ->where('due_date', '<', now())
            ->whereNull('completed_at')
            ->count();
        
        return [
            'total' => $totalTasks,
            'completed' => $completedTasks,
            'pending' => $pendingTasks,
            'overdue' => $overdueTasks,
            'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0,
        ];
    }

    /**
     * Obtenir la progression des tâches par colonne
     */
    private function getProjectTaskProgress(Project $project): array
    {
        $columns = $project->tasks()
            ->select('column', DB::raw('count(*) as count'))
            ->groupBy('column')
            ->get()
            ->pluck('count', 'column')
            ->toArray();
        
        return [
            'by_column' => $columns,
            'total_columns' => count($columns),
        ];
    }

    /**
     * Obtenir l'activité des membres du projet
     */
    private function getProjectMemberActivity(Project $project): array
    {
        $members = $project->members()->with('tasks')->get();
        $activity = [];
        
        foreach ($members as $member) {
            $tasksCreated = $member->tasks()->where('project_id', $project->id)->count();
            $tasksCompleted = $member->tasks()
                ->where('project_id', $project->id)
                ->whereNotNull('completed_at')
                ->count();
            
            $activity[] = [
                'user_id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'tasks_created' => $tasksCreated,
                'tasks_completed' => $tasksCompleted,
                'completion_rate' => $tasksCreated > 0 ? round(($tasksCompleted / $tasksCreated) * 100, 2) : 0,
            ];
        }
        
        return $activity;
    }

    /**
     * Obtenir la timeline du projet
     */
    private function getProjectTimeline(Project $project): array
    {
        $timeline = [];
        
        // Ajouter la création du projet
        $timeline[] = [
            'date' => $project->created_at->toISOString(),
            'event' => 'project_created',
            'description' => 'Projet créé',
        ];
        
        // Ajouter les tâches créées (limitées aux 10 dernières)
        $recentTasks = $project->tasks()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        foreach ($recentTasks as $task) {
            $timeline[] = [
                'date' => $task->created_at->toISOString(),
                'event' => 'task_created',
                'description' => "Tâche créée : {$task->title}",
                'task_id' => $task->id,
            ];
            
            if ($task->completed_at) {
                $timeline[] = [
                    'date' => $task->completed_at->toISOString(),
                    'event' => 'task_completed',
                    'description' => "Tâche terminée : {$task->title}",
                    'task_id' => $task->id,
                ];
            }
        }
        
        // Trier par date
        usort($timeline, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return array_slice($timeline, 0, 20); // Limiter à 20 événements
    }

    /**
     * Calculer la moyenne hebdomadaire de tâches complétées
     */
    private function calculateWeeklyAverage(User $user): float
    {
        $weeks = 4; // Calculer sur les 4 dernières semaines
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
} 