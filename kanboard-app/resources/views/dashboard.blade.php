<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold leading-tight text-gray-800 dark:text-gray-200">
                    {{ __('Dashboard') }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Vue d'ensemble de vos projets Kanban
                </p>
            </div>
            
            {{-- Actions rapides --}}
            <div class="flex gap-3">
                <a href="{{ route('projects.create') }}" 
                   class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-2.5 px-4 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 transform hover:scale-105 focus-ring">
                    <i class="fas fa-plus"></i>
                    Nouveau projet
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Statistiques rapides --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <i class="fas fa-project-diagram text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $projects->count() }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Projets actifs</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tasks text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $projects->sum('tasks_count') ?? 0 }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Tâches totales</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-purple-600 dark:text-purple-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            @php
                                $collaborationsCount = 0;
                                try {
                                    $collaborationsCount = auth()->user()->projectMembers()->count() + $projects->where('user_id', auth()->id())->count();
                                } catch (Exception $e) {
                                    $collaborationsCount = $projects->where('user_id', auth()->id())->count();
                                }
                            @endphp
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $collaborationsCount }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Collaborations</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section des projets --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Mes projets
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Gérez et suivez vos projets Kanban
                            </p>
                        </div>
                        @if($projects->count() > 0)
                            <a href="{{ route('projects.index') }}" 
                               class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium text-sm">
                                Voir tous les projets →
                            </a>
                        @endif
                    </div>
                </div>

                @if($projects->count() > 0)
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($projects->take(6) as $project)
                                <div class="group project-card bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl overflow-hidden hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-200 transform hover:scale-[1.02]">
                                    
                                    <a href="{{ route('projects.show', $project) }}" class="block p-6">
                                        <div class="flex flex-col h-full">
                                            {{-- En-tête du projet --}}
                                            <div class="flex items-start justify-between mb-3">
                                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-project-diagram text-white text-sm"></i>
                                                </div>
                                                @if($project->user_id === auth()->id())
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                        Propriétaire
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                        Membre
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Contenu du projet --}}
                                            <div class="flex-1">
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                    {{ $project->name }}
                                                </h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
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
                                    </a>
                                </div>
                            @endforeach

                            {{-- Carte d'ajout de projet --}}
                            <a href="{{ route('projects.create') }}" 
                               class="group project-card border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-blue-500 dark:hover:border-blue-400 bg-gray-50 dark:bg-gray-800/50 rounded-xl p-6 text-center h-full flex flex-col justify-center transition-all duration-200 hover:shadow-lg">
                                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-100 dark:group-hover:bg-blue-900 transition-colors">
                                    <i class="fas fa-plus text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 text-lg transition-colors"></i>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-600 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    Nouveau projet
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">
                                    Créer un nouveau projet Kanban
                                </p>
                            </a>
                        </div>

                        {{-- Voir plus de projets --}}
                        @if($projects->count() > 6)
                            <div class="text-center mt-8">
                                <a href="{{ route('projects.index') }}" 
                                   class="inline-flex items-center gap-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium transition-colors">
                                    Voir {{ $projects->count() - 6 }} autres projets
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                @else
                    {{-- État vide --}}
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-project-diagram text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                            Aucun projet pour le moment
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-sm mx-auto">
                            Créez votre premier projet Kanban pour commencer à organiser vos tâches et collaborer avec votre équipe.
                        </p>
                        <a href="{{ route('projects.create') }}" 
                           class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-4 px-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 focus-ring">
                            <i class="fas fa-plus"></i>
                            Créer mon premier projet
                        </a>
                    </div>
                @endif
            </div>
            </div>
            </div>
</x-app-layout>
