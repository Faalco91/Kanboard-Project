<?php
// resources/views/dashboard.blade.php
?>
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Tableau de bord') }}
            </h2>
            
            {{-- Bouton responsive qui va vers la page de création --}}
            <a href="{{ route('projects.create') }}" 
               class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-lg shadow-sm transition-all duration-200 hover:shadow-md w-full sm:w-auto justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                {{ __('Nouveau Projet') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Statistiques en cards responsive --}}
            @if(!$projects->isEmpty())
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
                    {{-- Total projets --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3 sm:ml-4">
                                    <dt class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                        Projets
                                    </dt>
                                    <dd class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ $projects->count() }}
                                    </dd>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Mes projets --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3 sm:ml-4">
                                    <dt class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                        Mes projets
                                    </dt>
                                    <dd class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ $projects->where('user_id', Auth::id())->count() }}
                                    </dd>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total tâches --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3 sm:ml-4">
                                    <dt class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                        Tâches
                                    </dt>
                                    <dd class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ $projects->sum('tasks_count') }}
                                    </dd>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Collaborations --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3 sm:ml-4">
                                    <dt class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                        Collaborations
                                    </dt>
                                    <dd class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ $projects->where('user_id', '!=', Auth::id())->count() }}
                                    </dd>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Section principale des projets --}}
            @if($projects->isEmpty())
                {{-- État vide responsive --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-8 sm:p-12 text-center">
                        <div class="mx-auto w-16 h-16 sm:w-24 sm:h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4 sm:mb-6">
                            <svg class="w-8 h-8 sm:w-12 sm:h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-medium text-gray-900 dark:text-gray-100 mb-2">
                            Bienvenue sur Kanboard !
                        </h3>
                        <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">
                            {{ __("Vous n'avez pas encore de projets. Créez votre premier projet pour commencer à organiser vos tâches.") }}
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center items-center">
                            <a href="{{ route('projects.create') }}" 
                               class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Créer mon premier projet
                            </a>
                            <a href="#demo" 
                               class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.01M15 10h1.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Voir la démo
                            </a>
                        </div>
                    </div>
                </div>
            @else
                {{-- En-tête des projets --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-gray-100">
                        Mes projets récents
                    </h3>
                    <a href="{{ route('projects.index') }}" 
                       class="mt-2 sm:mt-0 inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                        Voir tous les projets
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>

                {{-- Grid des projets responsive --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    @foreach($projects->take(6) as $project)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg hover:shadow-md transition-all duration-200 hover:-translate-y-1">
                            <a href="{{ route('projects.show', $project->id) }}" class="block h-full">
                                <div class="p-4 sm:p-6 h-full flex flex-col">
                                    {{-- En-tête du projet --}}
                                    <div class="flex justify-between items-start mb-3">
                                        <h4 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100 truncate pr-2">
                                            {{ $project->name }}
                                        </h4>
                                        <span class="text-xs px-2 py-1 rounded-full flex-shrink-0 {{ $project->user_id === Auth::id() ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                            {{ $project->user_id === Auth::id() ? 'Propriétaire' : 'Membre' }}
                                        </span>
                                    </div>

                                    {{-- Description --}}
                                    <div class="flex-1 mb-4">
                                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                            {{ Str::limit($project->description ?? 'Aucune description', 100) }}
                                        </p>
                                    </div>

                                    {{-- Métadonnées --}}
                                    <div class="flex justify-between items-center text-xs sm:text-sm text-gray-500 dark:text-gray-400 pt-3 border-t border-gray-100 dark:border-gray-700">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <span>{{ $project->tasks_count ?? 0 }} tâches</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="hidden sm:inline">{{ $project->updated_at->diffForHumans() }}</span>
                                            <span class="sm:hidden">{{ $project->updated_at->format('d/m') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @push('styles')
    <style>
        /* Truncate text avec ellipsis */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* Transitions fluides */
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 200ms;
        }
        
        /* Hover effects améliorés */
        .hover\:-translate-y-1:hover {
            transform: translateY(-0.25rem);
        }
        
        /* Mobile-first responsive adjustments */
        @media (max-width: 640px) {
            /* Réduire l'espacement sur mobile */
            .py-6 {
                padding-top: 1rem;
                padding-bottom: 1rem;
            }
            
            /* Cards plus compactes sur mobile */
            .grid > div {
                min-height: auto;
            }
        }
        
        /* Améliorations pour le dark mode */
        @media (prefers-color-scheme: dark) {
            .shadow-sm {
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
            }
            
            .hover\:shadow-md:hover {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.4), 0 2px 4px -1px rgba(0, 0, 0, 0.3);
            }
        }
    </style>
    @endpush
</x-app-layout>
