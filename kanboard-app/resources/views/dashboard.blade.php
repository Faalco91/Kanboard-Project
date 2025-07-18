<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold leading-tight text-gray-800 dark:text-gray-200">
                    {{ __('Tableau de bord') }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Bienvenue, {{ auth()->user()->name }} 👋
                </p>
            </div>
            
            {{-- Bouton de création --}}
            <a href="{{ route('projects.create') }}" 
               class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 w-full sm:w-auto justify-center focus-ring">
                <i class="fas fa-plus"></i>
                {{ __('Nouveau Projet') }}
            </a>
        </div>
    </x-slot>

    @push('styles')
        <style>
            .stats-card {
                @apply bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700;
                @apply p-6 transition-all duration-200 hover:shadow-md hover:scale-105;
            }
            
            .project-card {
                @apply bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700;
                @apply overflow-hidden hover:shadow-lg transition-all duration-300 hover:scale-105;
                @apply group cursor-pointer;
            }
            
            .gradient-bg {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
            
            .fade-in {
                animation: fadeIn 0.6s ease-in-out;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
    @endpush

    <div class="py-6 sm:py-12 fade-in">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Statistiques en cards --}}
            @if(!$projects->isEmpty())
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
                    {{-- Total projets --}}
                    <div class="stats-card">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-folder text-white text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $projects->count() }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $projects->count() > 1 ? 'Projets' : 'Projet' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Projets où je suis propriétaire --}}
                    <div class="stats-card">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-crown text-white text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ $projects->where('user_id', auth()->id())->count() }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Propriétaire</div>
                            </div>
                        </div>
                    </div>

                    {{-- Projets où je suis membre --}}
                    <div class="stats-card">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-users text-white text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ $projects->where('user_id', '!=', auth()->id())->count() }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Membre</div>
                            </div>
                        </div>
                    </div>

                    {{-- Total tâches --}}
                    <div class="stats-card">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-tasks text-white text-lg"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ $projects->sum('tasks_count') ?? 0 }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Tâches</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Section des projets --}}
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ $projects->isEmpty() ? 'Créez votre premier projet' : 'Mes projets récents' }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {{ $projects->isEmpty() ? 'Commencez à organiser vos idées avec Kanboard' : 'Accédez rapidement à vos projets favoris' }}
                        </p>
                    </div>
                    
                    @if(!$projects->isEmpty())
                        <a href="{{ route('projects.index') }}" 
                           class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium text-sm flex items-center gap-2 transition-colors">
                            Voir tous les projets
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    @endif
                </div>

                @if($projects->isEmpty())
                    {{-- État vide --}}
                    <div class="text-center py-12">
                        <div class="mx-auto w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mb-6">
                            <i class="fas fa-rocket text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                            Prêt à commencer ?
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">
                            Créez votre premier projet et commencez à organiser vos tâches avec la méthode Kanban.
                        </p>
                        <a href="{{ route('projects.create') }}" 
                           class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 focus-ring">
                            <i class="fas fa-plus"></i>
                            Créer mon premier projet
                        </a>
                    </div>
                @else
                    {{-- Grid des projets --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($projects->take(6) as $project)
                            <a href="{{ route('projects.show', $project->id) }}" class="project-card">
                                <div class="p-6">
                                    {{-- En-tête du projet --}}
                                    <div class="flex justify-between items-start mb-4">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2">
                                            {{ $project->name }}
                                        </h4>
                                        <div class="flex-shrink-0 ml-2">
                                            @if($project->user_id === auth()->id())
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    <i class="fas fa-crown mr-1"></i>
                                                    Propriétaire
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    <i class="fas fa-user mr-1"></i>
                                                    Membre
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Description --}}
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3">
                                            {{ $project->description ? Str::limit($project->description, 120) : 'Aucune description disponible' }}
                                        </p>
                                    </div>

                                    {{-- Métadonnées --}}
                                    <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400 pt-4 border-t border-gray-100 dark:border-gray-700">
                                        <div class="flex items-center">
                                            <i class="fas fa-tasks mr-1"></i>
                                            <span>{{ $project->tasks_count ?? 0 }} tâches</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar mr-1"></i>
                                            <span>{{ $project->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Barre de progression (optionnelle) --}}
                                @if(isset($project->tasks_count) && $project->tasks_count > 0)
                                    <div class="px-6 pb-4">
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="bg-gradient-to-r from-blue-500 to-green-500 h-2 rounded-full" 
                                                 style="width: {{ rand(20, 80) }}%"></div>
                                        </div>
                                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <span>Progression</span>
                                            <span>{{ rand(20, 80) }}%</span>
                                        </div>
                                    </div>
                                @endif
                            </a>
                        @endforeach

                        {{-- Carte d'ajout de projet --}}
                        <a href="{{ route('projects.create') }}" 
                           class="project-card border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-blue-500 dark:hover:border-blue-400 bg-gray-50 dark:bg-gray-800/50">
                            <div class="p-6 text-center">
                                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-100 dark:group-hover:bg-blue-900 transition-colors">
                                    <i class="fas fa-plus text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 text-lg transition-colors"></i>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-600 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    Nouveau projet
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">
                                    Créer un nouveau projet Kanban
                                </p>
                            </div>
                        </a>
                    </div>

                    {{-- Voir plus de projets --}}
                    @if($projects->count() > 6)
                        <div class="text-center mt-8">
                            <a href="{{ route('projects.index') }}" 
                               class="inline-flex items-center gap-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium transition-colors">
                                Voir {{ $projects->count() - 6 }} autres projets
                                <i class="fas fa-arrow-right text-sm"></i>
                            </a>
                        </div>
                    @endif
                @endif
            </div>

            {{-- Section d'aide rapide --}}
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-700 rounded-xl p-6 border border-blue-200 dark:border-gray-600">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-center sm:text-left">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                            <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                            Conseil du jour
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Utilisez les colonnes pour organiser vos tâches : "À faire", "En cours", "Terminé"
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="#" 
                           class="inline-flex items-center gap-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium py-2 px-4 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-600 transition-colors">
                            <i class="fas fa-question-circle"></i>
                            Aide
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
