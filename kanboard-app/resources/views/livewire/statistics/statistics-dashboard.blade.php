<div class="py-6 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- En-tête avec filtres --}}
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                        📊 Statistiques
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">
                        Analysez votre productivité et suivez vos projets
                    </p>
                </div>
                

            </div>
        </div>

        @if($loading)
            <div class="flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            </div>
        @else
            {{-- Cartes de statistiques principales --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                {{-- Projets --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-folder text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ $stats['projects']['total'] ?? 0 }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Projets totaux</div>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                        {{ $stats['projects']['owned'] ?? 0 }} créés • {{ $stats['projects']['member'] ?? 0 }} membres
                    </div>
                </div>

                {{-- Tâches --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-tasks text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ $stats['tasks']['total_assigned'] ?? 0 }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Tâches assignées</div>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                        {{ $stats['tasks']['completed'] ?? 0 }} terminées • {{ $stats['tasks']['completion_rate'] ?? 0 }}% taux
                    </div>
                </div>

                {{-- Productivité --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-chart-line text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ $stats['productivity']['completed_this_week'] ?? 0 }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Terminées cette semaine</div>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                        Moyenne: {{ $stats['productivity']['weekly_average'] ?? 0 }}/semaine
                    </div>
                </div>

                {{-- Tâches en retard --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ $stats['tasks']['overdue'] ?? 0 }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Tâches en retard</div>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                        À traiter en priorité
                    </div>
                </div>
            </div>

            {{-- Graphiques et détails --}}
            {{-- Statistiques de productivité détaillées --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    🚀 Productivité
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Terminées cette semaine</span>
                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $stats['productivity']['completed_this_week'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Terminées ce mois</span>
                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $stats['productivity']['completed_this_month'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Créées cette semaine</span>
                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $stats['productivity']['created_this_week'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Moyenne hebdomadaire</span>
                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $stats['productivity']['weekly_average'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            {{-- Conseils et recommandations --}}
            <div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    💡 Conseils pour améliorer votre productivité
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-400">
                    @if(($stats['tasks']['overdue'] ?? 0) > 0)
                        <div class="flex items-start gap-2">
                            <i class="fas fa-exclamation-circle text-red-500 mt-1"></i>
                            <span>Vous avez {{ $stats['tasks']['overdue'] }} tâches en retard. Concentrez-vous sur les priorités.</span>
                        </div>
                    @endif
                    
                    @if(($stats['tasks']['completion_rate'] ?? 0) < 50)
                        <div class="flex items-start gap-2">
                            <i class="fas fa-lightbulb text-yellow-500 mt-1"></i>
                            <span>Votre taux de completion est faible. Essayez de terminer plus de tâches.</span>
                        </div>
                    @endif
                    
                    @if(($stats['productivity']['completed_this_week'] ?? 0) < 3)
                        <div class="flex items-start gap-2">
                            <i class="fas fa-target text-blue-500 mt-1"></i>
                            <span>Fixez-vous un objectif de 3-5 tâches par semaine pour maintenir votre rythme.</span>
                        </div>
                    @endif
                    
                    @if(($stats['projects']['active'] ?? 0) > 5)
                        <div class="flex items-start gap-2">
                            <i class="fas fa-balance-scale text-purple-500 mt-1"></i>
                            <span>Vous avez beaucoup de projets actifs. Concentrez-vous sur les plus importants.</span>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div> 