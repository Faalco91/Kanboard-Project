<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixProjectOwners extends Command
{
    protected $signature = 'projects:fix-owners';
    protected $description = 'Ajoute les propriétaires manquants comme membres de leurs projets';

    public function handle()
    {
        $this->info('Début de la correction des propriétaires de projets...');

        $projects = Project::all();
        $count = 0;

        foreach ($projects as $project) {
            // Vérifie si le propriétaire est déjà membre
            $isMember = DB::table('project_members')
                ->where('project_id', $project->id)
                ->where('user_id', $project->user_id)
                ->exists();

            if (!$isMember) {
                DB::table('project_members')->insert([
                    'project_id' => $project->id,
                    'user_id' => $project->user_id,
                    'role' => 'owner',
                    'status' => 'accepted',
                    'invitation_accepted_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $count++;
            }
        }

        $this->info("Correction terminée ! $count propriétaires ont été ajoutés comme membres.");
    }
} 