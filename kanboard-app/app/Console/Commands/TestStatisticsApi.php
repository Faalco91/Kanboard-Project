<?php

namespace App\Console\Commands;

use App\Http\Controllers\API\StatisticsController;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class TestStatisticsApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:statistics-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test de l\'API des statistiques';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Test de l\'API des statistiques...');
        
        // Récupérer le premier utilisateur
        $user = User::first();
        
        if (!$user) {
            $this->error('❌ Aucun utilisateur trouvé dans la base de données');
            return 1;
        }
        
        $this->info("👤 Utilisateur de test : {$user->name} ({$user->email})");
        
        // Simuler l'authentification
        auth()->login($user);
        
        // Créer une requête simulée
        $request = new Request();
        
        // Créer une instance du contrôleur
        $controller = new StatisticsController();
        
        try {
            // Tester la méthode dashboard
            $this->info('📊 Test de la méthode dashboard...');
            $response = $controller->dashboard($request);
            $data = json_decode($response->getContent(), true);
            
            if ($data['success']) {
                $this->info('✅ API des statistiques fonctionne correctement !');
                $this->info('📈 Données retournées :');
                
                // Afficher les statistiques de manière formatée
                $this->table(
                    ['Métrique', 'Valeur'],
                    [
                        ['Projets totaux', $data['data']['projects']['total']],
                        ['Projets créés', $data['data']['projects']['owned']],
                        ['Projets membres', $data['data']['projects']['member']],
                        ['Projets actifs', $data['data']['projects']['active']],
                        ['Tâches créées', $data['data']['tasks']['total_created']],
                        ['Tâches assignées', $data['data']['tasks']['total_assigned']],
                        ['Tâches complétées', $data['data']['tasks']['completed']],
                        ['Tâches en retard', $data['data']['tasks']['overdue']],
                        ['Taux de completion', $data['data']['tasks']['completion_rate'] . '%'],
                        ['Complétées cette semaine', $data['data']['productivity']['completed_this_week']],
                        ['Complétées ce mois', $data['data']['productivity']['completed_this_month']],
                        ['Moyenne hebdomadaire', $data['data']['productivity']['weekly_average']],
                    ]
                );
                
                return 0;
            } else {
                $this->error('❌ L\'API a retourné une erreur');
                $this->error($data['message'] ?? 'Erreur inconnue');
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors du test :');
            $this->error($e->getMessage());
            $this->error("Fichier : {$e->getFile()}");
            $this->error("Ligne : {$e->getLine()}");
            return 1;
        }
    }
} 