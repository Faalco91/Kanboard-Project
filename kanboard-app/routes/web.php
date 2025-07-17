<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\RobotsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ===== ROUTES PUBLIQUES =====

// Page d'accueil (publique)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// SEO Routes (publiques - accessibles aux moteurs de recherche)
Route::get('/sitemap.xml', [SitemapController::class, 'index'])
    ->name('sitemap')
    ->middleware(['throttle:60,1']); // Limitation du taux de requêtes

Route::get('/robots.txt', [RobotsController::class, 'index'])
    ->name('robots');

// Route pour voir l'invitation (accessible sans authentification avec signature)
Route::get('/projects/{project}/invitation', [ProjectMemberController::class, 'showInvitation'])
     ->name('project.members.show-invitation')
     ->middleware(['signed']);

// ===== ROUTES AUTHENTIFIÉES =====

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        $user = Auth::user();
        
        // Récupérer les projets avec le nombre de tâches et le rôle de l'utilisateur
        $projects = $user->projects()
            ->withCount('tasks')
            ->with(['members' => function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->select('users.id', 'project_members.role');
            }])
            ->get();
            
        return view('dashboard', compact('projects'));
    })->name('dashboard');

    // ===== PROFILE ROUTES =====
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // ===== PROJECT ROUTES =====
    Route::resource('projects', ProjectController::class);
    Route::get('/projects/{project}/calendar', [ProjectController::class, 'calendar'])->name('projects.calendar');
    Route::get('/projects/{project}/list', [ProjectController::class, 'list'])->name('projects.list');
    Route::get('/projects/{project}/export-ical', [ProjectController::class, 'exportICalendar'])->name('projects.export-ical');
    
    // Project members routes
    Route::prefix('projects/{project}')->name('project.')->group(function () {
        
        // Members management
        Route::prefix('members')->name('members.')->group(function () {
            Route::get('/', [ProjectMemberController::class, 'index'])->name('index');
            Route::post('/invite', [ProjectMemberController::class, 'invite'])->name('invite');
            Route::post('/{user}/accept', [ProjectMemberController::class, 'accept'])->name('accept');
            Route::post('/{user}/decline', [ProjectMemberController::class, 'decline'])->name('decline');
            Route::delete('/{user}', [ProjectMemberController::class, 'remove'])->name('remove');
        });
        
        // Invitation responses
        Route::post('/accept-invitation', [ProjectMemberController::class, 'acceptInvitation'])
             ->name('members.accept-invitation');
             
        Route::post('/reject-invitation', [ProjectMemberController::class, 'rejectInvitation'])
             ->name('members.reject-invitation');
    });

    // ===== TASK ROUTES =====
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::post('/', [TaskController::class, 'store'])->name('store');
        Route::put('/{task}', [TaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
    });
});

// ===== ROUTES D'AUTHENTIFICATION =====
require __DIR__.'/auth.php';
