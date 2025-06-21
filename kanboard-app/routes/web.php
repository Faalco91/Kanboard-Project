<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectMemberController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

// Route pour voir l'invitation (accessible sans authentification)
Route::get('/projects/{project}/invitation', [ProjectMemberController::class, 'showInvitation'])
     ->name('project.members.show-invitation')
     ->middleware(['signed']);

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

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Project routes
    Route::resource('projects', ProjectController::class);
    
    // Project members routes
    Route::prefix('projects/{project}/members')->group(function () {
        Route::get('/', [ProjectMemberController::class, 'index'])->name('project.members.index');
        Route::post('/invite', [ProjectMemberController::class, 'invite'])->name('project.members.invite');
        Route::post('/{user}/accept', [ProjectMemberController::class, 'accept'])->name('project.members.accept');
        Route::post('/{user}/decline', [ProjectMemberController::class, 'decline'])->name('project.members.decline');
        Route::delete('/{user}', [ProjectMemberController::class, 'remove'])->name('project.members.remove');
    });
         
    Route::post('/projects/{project}/accept-invitation', [ProjectMemberController::class, 'acceptInvitation'])
         ->name('project.members.accept-invitation');
         
    Route::post('/projects/{project}/reject-invitation', [ProjectMemberController::class, 'rejectInvitation'])
         ->name('project.members.reject-invitation');

    // Task routes    
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
});

require __DIR__.'/auth.php';
