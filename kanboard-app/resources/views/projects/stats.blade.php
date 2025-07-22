<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold leading-tight text-gray-800 dark:text-gray-200">
                    Statistiques du projet
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Analyse des performances de {{ $project->name }}
                </p>
            </div>
            
            <div class="flex gap-3">
                <a href="{{ route('projects.show', $project) }}" 
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors">
                    <i class="fas fa-columns mr-1"></i>
                    Vue Kanban
                </a>
                <a href="{{ route('projects.edit', $project) }}" 
                   class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    Retour aux paramètres
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Aperçu général --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                {{-- Total des tâches --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tasks text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total_tasks'] }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Tâches totales</p>
                        </div>
                    </div>
                </div>

                {{-- Tâches terminées --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['completed_tasks'] }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Tâches terminées</p>
                        </div>
                    </div>
                    @if($stats['total_tasks'] > 0)
                        <div class="mt-4">
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" 
                                     style="width: {{ ($stats['completed_tasks'] / $stats['total_tasks']) * 100 }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 text-right">
                                {{ round(($stats['completed_tasks'] / $stats['total_tasks']) * 100, 1) }}%
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Tâches en cours --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-orange-600 dark:text-orange-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['in_progress_tasks'] }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">En cours</p>
                        </div>
                    </div>
                </div>

                {{-- Tâches en retard --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900/50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['overdue_tasks'] }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">En retard</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Graphiques détaillés --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                
                {{-- Répartition par statut --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
                        <i class="fas fa-chart-pie mr-2 text-blue-600 dark:text-blue-400"></i>
                        Répartition par statut
                    </h3>
                    
                    @if(count($stats['tasks_by_column']) > 0)
                        <div class="space-y-4">
                            @foreach($stats['tasks_by_column'] as $column => $count)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 rounded-full mr-3
                                            @if($column === 'Backlog') bg-gray-500
                                            @elseif($column === 'To Do') bg-blue-500
                                            @elseif($column === 'In Progress') bg-orange-500
                                            @elseif($column === 'To Be Checked') bg-purple-500
                                            @else bg-green-500
                                            @endif">
                                        </div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $column }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-sm font-bold text-gray-900 dark:text-gray-100 mr-3">{{ $count }}</span>
                                        <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="h-2 rounded-full
                                                @if($column === 'Backlog') bg-gray-500
                                                @elseif($column === 'To Do') bg-blue-500
                                                @elseif($column === 'In Progress') bg-orange-500
                                                @elseif($column === 'To Be Checked') bg-purple-500
                                                @else bg-green-500
                                                @endif" 
                                                 style="width: {{ $stats['total_tasks'] > 0 ? ($count / $stats['total_tasks']) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-chart-pie text-gray-400 text-3xl mb-4"></i>
                            <p class="text-gray-500 dark:text-gray-400">Aucune donnée de statut disponible</p>
                        </div>
                    @endif
                </div>

                {{-- Répartition par catégorie --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
                        <i class="fas fa-tags mr-2 text-green-600 dark:text-green-400"></i>
                        Répartition par catégorie
                    </h3>
                    
                    @if(count($stats['tasks_by_category']) > 0)
                        <div class="space-y-4">
                            @foreach($stats['tasks_by_category'] as $category => $count)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 bg-blue-500 rounded-full mr-3"></div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $category }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-sm font-bold text-gray-900 dark:text-gray-100 mr-3">{{ $count }}</span>
                                        <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="bg-blue-500 h-2 rounded-full" 
                                                 style="width: {{ $stats['total_tasks'] > 0 ? ($count / $stats['total_tasks']) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-tags text-gray-400 text-3xl mb-4"></i>
                            <p class="text-gray-500 dark:text-gray-400">Aucune catégorie définie</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Répartition par utilisateur --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
        <i class="fas fa-users mr-2 text-purple-600 dark:text-purple-400"></i>
        Répartition par membre
    </h3>
    
    @if(count($stats['tasks_by_user']) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($stats['tasks_by_user'] as $user => $count)
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-100 dark:border-gray-600">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/50 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user text-purple-600 dark:text-purple-400 text-sm"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user }}</span>
                        </div>
                        <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $count }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 mb-1">
                        <div class="bg-purple-500 h-2 rounded-full" 
                             style="width: {{ $stats['total_tasks'] > 0 ? ($count / $stats['total_tasks']) * 100 : 0 }}%">
                        </div>
                    </div>
                    <p class="text-xs text-gray-600 dark:text-gray-300 text-right">
                        {{ $stats['total_tasks'] > 0 ? round(($count / $stats['total_tasks']) * 100, 1) : 0 }}%
                    </p>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <i class="fas fa-users text-gray-400 text-3xl mb-4"></i>
            <p class="text-gray-500 dark:text-gray-400">Aucune tâche assignée</p>
        </div>
    @endif
</div>

            {{-- Résumé du projet --}}
            <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">
                    <i class="fas fa-info-circle mr-2"></i>
                    Résumé du projet
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <h4 class="font-medium text-blue-800 dark:text-blue-200 mb-2">Informations générales</h4>
                        <ul class="space-y-1 text-blue-700 dark:text-blue-300">
                            <li><strong>Nom :</strong> {{ $project->name }}</li>
                            <li><strong>Créé le :</strong> {{ $project->created_at->format('d/m/Y à H:i') }}</li>
                            <li><strong>Dernière mise à jour :</strong> {{ $project->updated_at->format('d/m/Y à H:i') }}</li>
                            <li><strong>Propriétaire :</strong> {{ $project->user->name }}</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-medium text-blue-800 dark:text-blue-200 mb-2">Performance</h4>
                        <ul class="space-y-1 text-blue-700 dark:text-blue-300">
                            <li><strong>Taux de completion :</strong> 
                                {{ $stats['total_tasks'] > 0 ? round(($stats['completed_tasks'] / $stats['total_tasks']) * 100, 1) : 0 }}%
                            </li>
                            <li><strong>Tâches en retard :</strong> {{ $stats['overdue_tasks'] }}</li>
                            <li><strong>Catégories utilisées :</strong> {{ count($stats['tasks_by_category']) }}</li>
                            <li><strong>Membres actifs :</strong> {{ count($stats['tasks_by_user']) }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
    <a href="{{ route('projects.export-ical', $project) }}" 
       class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition-colors shadow-lg border border-green-700">
        <i class="fas fa-download"></i>
        Exporter au format iCal
    </a>
    <a href="{{ route('projects.list', $project) }}" 
       class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-6 rounded-lg transition-colors shadow-lg border border-gray-700">
        <i class="fas fa-list"></i>
        Vue Liste
    </a>
    <a href="{{ route('projects.calendar', $project) }}" 
       class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white font-medium py-3 px-6 rounded-lg transition-colors shadow-lg border border-purple-700">
        <i class="fas fa-calendar"></i>
        Vue Calendrier
    </a>
</div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Animations pour les barres de progression */
            .bg-green-500, .bg-blue-500, .bg-orange-500, .bg-purple-500, .bg-gray-500 {
                transition: width 0.8s ease-in-out;
            }
            
            /* Effet hover sur les cartes */
            .bg-white:hover, .dark .bg-gray-800:hover {
                transform: translateY(-2px);
                transition: transform 0.2s ease;
            }
    .bg-purple-600 {
        background-color: #7c3aed !important;
        box-shadow: 0 10px 15px -3px rgba(124, 58, 237, 0.3), 0 4px 6px -2px rgba(124, 58, 237, 0.1) !important;
    }
    
    .bg-green-600 {
        background-color: #16a34a !important;
        box-shadow: 0 10px 15px -3px rgba(22, 163, 74, 0.3), 0 4px 6px -2px rgba(22, 163, 74, 0.1) !important;
    }
    
    .bg-gray-600 {
        background-color: #4b5563 !important;
        box-shadow: 0 10px 15px -3px rgba(75, 85, 99, 0.3), 0 4px 6px -2px rgba(75, 85, 99, 0.1) !important;
    }
        </style>
    @endpush
</x-app-layout>
