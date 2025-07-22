<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold leading-tight text-gray-800 dark:text-gray-200">
                    {{ __('Mes Projets') }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Gérez tous vos projets Kanban en un seul endroit
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                {{-- Barre de recherche --}}
                <div class="relative">
                    <input type="text" 
                           id="searchInput"
                           placeholder="Rechercher un projet..." 
                           class="w-full sm:w-64 pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100 transition-colors">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                
                {{-- Bouton de création --}}
                <a href="{{ route('projects.create') }}" 
                   class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-2.5 px-4 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 transform hover:scale-105 w-full sm:w-auto justify-center focus-ring">
                    <i class="fas fa-plus"></i>
                    Nouveau projet
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Messages de succès/erreur --}}
            @if(session('success'))
                <div class="mb-6 bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <div class="flex">
                        <i class="fas fa-check-circle text-green-400 mr-3 mt-0.5"></i>
                        <p class="text-green-800 dark:text-green-200">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            {{-- Grille des projets --}}
            <div id="projectsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($projects as $project)
                    <div class="project-item bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-200 transform hover:scale-[1.02]"
                         data-project-name="{{ strtolower($project->name) }}"
                         data-project-description="{{ strtolower($project->description ?? '') }}">
                        
                        {{-- Image d'en-tête ou icône --}}
                        <div class="bg-gradient-to-br from-blue-500 to-purple-600 h-24 flex items-center justify-center">
                            <i class="fas fa-project-diagram text-white text-2xl"></i>
                        </div>

                        {{-- Contenu du projet --}}
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-3">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex-1">
                                    {{ $project->name }}
                                </h3>
                                
                                {{-- Menu d'actions --}}
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" 
                                            class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    
                                    <div x-show="open" 
                                         @click.away="open = false"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 z-10">
                                        
                                        <div class="py-2">
                                            <a href="{{ route('projects.show', $project) }}" 
                                               class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                <i class="fas fa-eye mr-3"></i>
                                                Voir le projet
                                            </a>
                                            
                                            @if($project->user_id === auth()->id())
                                                <a href="{{ route('projects.edit', $project) }}" 
                                                   class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                    <i class="fas fa-cog mr-3"></i>
                                                    Paramètres
                                                </a>
                                                
                                                <a href="{{ route('project.members.index', $project) }}" 
                                                   class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                    <i class="fas fa-users mr-3"></i>
                                                    Gérer les membres
                                                </a>
                                                
                                                <div class="border-t border-gray-200 dark:border-gray-600 my-2"></div>
                                                
                                                <form action="{{ route('projects.destroy', $project) }}" method="POST" 
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                                        <i class="fas fa-trash mr-3"></i>
                                                        Supprimer
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Description --}}
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">
                                {{ $project->description ? Str::limit($project->description, 100) : 'Aucune description disponible' }}
                            </p>

                            {{-- Métadonnées --}}
                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-tasks mr-1"></i>
                                    <span>{{ $project->tasks_count ?? 0 }} tâches</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-users mr-1"></i>
                                    <span>{{ $project->members_count ?? 1 }} membres</span>
                                </div>
                            </div>

                            {{-- Statut et actions --}}
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
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
                                
                                {{-- Indicateur d'activité récente --}}
                                @if($project->updated_at->diffInHours() < 24)
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                        <span class="text-xs text-green-600 dark:text-green-400 ml-1">Actif</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Bouton d'accès --}}
                            <div class="mt-4">
                                <a href="{{ route('projects.show', $project) }}" 
                                   class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    <i class="fas fa-arrow-right mr-2"></i>
                                    Accéder au projet
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Carte d'ajout de projet --}}
                <div class="project-card border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-blue-500 dark:hover:border-blue-400 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                    <a href="{{ route('projects.create') }}" class="block h-full">
                        <div class="p-6 text-center h-full flex flex-col justify-center min-h-[300px]">
                            <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-100 dark:group-hover:bg-blue-900 transition-colors">
                                <i class="fas fa-plus text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 text-xl transition-colors"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-600 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors mb-2">
                                Nouveau projet
                            </h4>
                            <p class="text-sm text-gray-500 dark:text-gray-500">
                                Créer un nouveau projet Kanban
                            </p>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Message aucun résultat --}}
            <div id="noResults" class="hidden text-center py-12">
                <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-search text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                    Aucun projet trouvé
                </h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Essayez de modifier votre recherche ou créez un nouveau projet.
                </p>
            </div>

            {{-- État vide --}}
            @if($projects->count() === 0)
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

    @push('styles')
        <style>
            .project-hidden {
                display: none;
            }
            
            .project-item {
                transition: all 0.3s ease;
            }
            
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            
            .focus-ring:focus {
                outline: none;
                ring: 2px;
                ring-color: rgb(59 130 246);
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Fonctionnalité de recherche
                const searchInput = document.getElementById('searchInput');
                const projectItems = document.querySelectorAll('.project-item');
                const noResults = document.getElementById('noResults');
                const projectsGrid = document.getElementById('projectsGrid');
                
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    let visibleCount = 0;
                    
                    projectItems.forEach(project => {
                        const name = project.getAttribute('data-project-name');
                        const description = project.getAttribute('data-project-description');
                        
                        if (name.includes(searchTerm) || description.includes(searchTerm)) {
                            project.classList.remove('project-hidden');
                            visibleCount++;
                        } else {
                            project.classList.add('project-hidden');
                        }
                    });
                    
                    // Afficher/masquer le message "aucun résultat"
                    if (visibleCount === 0 && searchTerm !== '') {
                        noResults.classList.remove('hidden');
                        projectsGrid.classList.add('hidden');
                    } else {
                        noResults.classList.add('hidden');
                        projectsGrid.classList.remove('hidden');
                    }
                });
                
                // Effacer la recherche avec Escape
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        this.value = '';
                        this.dispatchEvent(new Event('input'));
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
