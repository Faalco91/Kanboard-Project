<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Mes Projets') }}
            </h2>
            <a href="{{ route('projects.create') }}" 
               class="btn btn-primary w-full sm:w-auto">
                <i class="fas fa-plus mr-2"></i>
                {{ __('Nouveau projet') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($projects->count() > 0)
                {{-- Grid responsive de projets --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($projects as $project)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-300">
                            <div class="p-6">
                                {{-- En-tête du projet --}}
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $project->name }}
                                    </h3>
                                    <div class="flex items-center gap-2">
                                        {{-- Badge du rôle --}}
                                        @if($project->user_id === auth()->id())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                <i class="fas fa-crown mr-1"></i>
                                                Propriétaire
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                <i class="fas fa-user mr-1"></i>
                                                Membre
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Description --}}
                                @if($project->description)
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">
                                        {{ $project->description }}
                                    </p>
                                @endif

                                {{-- Statistiques --}}
                                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-tasks mr-1"></i>
                                        <span>{{ $project->tasks_count ?? 0 }} tâches</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-users mr-1"></i>
                                        <span>{{ $project->members_count ?? 1 }} membres</span>
                                    </div>
                                </div>

                                {{-- Date de mise à jour --}}
                                <div class="text-xs text-gray-400 dark:text-gray-500 mb-4">
                                    Mis à jour {{ $project->updated_at->diffForHumans() }}
                                </div>

                                {{-- Actions --}}
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <a href="{{ route('projects.show', $project) }}" 
                                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-md text-sm font-medium transition-colors duration-200">
                                        <i class="fas fa-eye mr-2"></i>
                                        Voir le projet
                                    </a>
                                    
                                    @if($project->user_id === auth()->id())
                                        <div class="flex gap-2">
                                            <a href="{{ route('projects.edit', $project) }}" 
                                               class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-3 rounded-md text-sm transition-colors duration-200"
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('projects.destroy', $project) }}" 
                                                  class="inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="bg-red-600 hover:bg-red-700 text-white py-2 px-3 rounded-md text-sm transition-colors duration-200"
                                                        title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- État vide --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6">
                            <i class="fas fa-folder-open text-4xl text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                            Aucun projet pour le moment
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6">
                            Commencez par créer votre premier projet pour organiser vos tâches.
                        </p>
                        <a href="{{ route('projects.create') }}" 
                           class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Créer mon premier projet
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2563eb;
        }
        
        @media (max-width: 640px) {
            .btn {
                width: 100%;
            }
        }
    </style>
    @endpush
</x-app-layout>
