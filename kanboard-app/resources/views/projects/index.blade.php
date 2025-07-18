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
                           placeholder="Rechercher un projet..." 
                           class="w-full sm:w-64 pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100 transition-colors"
                           x-data="projectSearch()" 
                           x-model="searchTerm" 
                           @input="filterProjects()">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                
                {{-- Bouton de création --}}
                <a href="{{ route('projects.create') }}" 
                   class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-2.5 px-4 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 transform hover:scale-105 w-full sm:w-auto justify-center focus-ring">
                    <i class="fas fa-plus"></i>
                    {{ __('Nouveau projet') }}
                </a>
            </div>
        </div>
    </x-slot>

    @push('styles')
        <style>
            .project-card {
                @apply bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700;
                @apply overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1;
                @apply group;
            }
            
            .filter-tag {
                @apply inline-flex items-center px-3 py-1 rounded-full text-xs font-medium cursor-pointer transition-colors;
            }
            
            .filter-tag.active {
                @apply bg-blue-600 text-white;
            }
            
            .filter-tag:not(.active) {
                @apply bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600;
            }
            
            .project-hidden {
                @apply hidden;
            }
            
            .fade-in-up {
                animation: fadeInUp 0.6s ease-out;
            }
            
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    @endpush

    <div class="py-6 sm:py-12 fade-in-up">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if($projects->count() > 0)
                {{-- Filtres et statistiques --}}
                <div class="mb-8">
                    {{-- Statistiques rapides --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-folder text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <div>
                                    <div class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $projects->count() }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Total</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-crown text-green-600 dark:text-green-400"></i>
                                </div>
                                <div>
                                    <div class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ $projects->where('user_id', auth()->id())->count() }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Propriétaire</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-users text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <div>
                                    <div class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ $projects->where('user_id', '!=', auth()->id())->count() }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Membre</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-tasks text-orange-600 dark:text-orange-400"></i>
                                </div>
                                <div>
                                    <div class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ $projects->sum('tasks_count') ?? 0 }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Tâches</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Filtres --}}
                    <div class="flex flex-wrap gap-2 mb-6" x-data="projectFilters()">
                        <button @click="setFilter('all')" 
                                class="filter-tag" 
                                :class="{ 'active': currentFilter === 'all' }">
                            <i class="fas fa-th-large mr-1"></i>
                            Tous les projets
                        </button>
                        <button @click="setFilter('owner')" 
                                class="filter-tag" 
                                :class="{ 'active': currentFilter === 'owner' }">
                            <i class="fas fa-crown mr-1"></i>
                            Mes projets
                        </button>
                        <button @click="setFilter('member')" 
                                class="filter-tag" 
                                :class="{ 'active': currentFilter === 'member' }">
                            <i class="fas fa-users mr-1"></i>
                            Projets partagés
                        </button>
                    </div>
                </div>

                {{-- Grid des projets --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="projectsGrid">
                    @foreach($projects as $project)
                        <div class="project-card project-item" 
                             data-project-name="{{ strtolower($project->name) }}"
                             data-project-description="{{ strtolower($project->description ?? '') }}"
                             data-project-role="{{ $project->user_id === auth()->id() ? 'owner' : 'member' }}">
                            
                            <a href="{{ route('projects.show', $project->id) }}" class="block">
                                <div class="p-6">
                                    {{-- En-tête du projet --}}
                                    <div class="flex justify-between items-start mb-4">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2 pr-2">
                                            {{ $project->name }}
                                        </h4>
                                        <div class="flex-shrink-0">
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
                                            {{ $project->description ? Str::limit($project->description, 100) : 'Aucune description disponible' }}
                                        </p>
                                    </div>

                                    {{-- Statistiques du projet --}}
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                            <div class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                                {{ $project->tasks_count ?? 0 }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Tâches</div>
                                        </div>
                                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                            <div class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                                {{ $project->members_count ?? 1 }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Membres</div>
                                        </div>
                                    </div>

                                    {{-- Barre de progression --}}
                                    @php
                                        $progress = rand(15, 85); // Remplacer par une vraie logique
                                    @endphp
                                    <div class="mb-4">
                                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                            <span>Progression</span>
                                            <span>{{ $progress }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="bg-gradient-to-r from-blue-500 to-green-500 h-2 rounded-full transition-all duration-500" 
                                                 style="width: {{ $progress }}%"></div>
                                        </div>
                                    </div>

                                    {{-- Métadonnées --}}
                                    <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400 pt-4 border-t border-gray-100 dark:border-gray-700">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar mr-1"></i>
                                            <span>{{ $project->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-clock mr-1"></i>
                                            <span>{{ $project->updated_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            {{-- Actions rapides --}}
                            <div class="px-6 pb-4 flex justify-between items-center border-t border-gray-100 dark:border-gray-700 pt-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('projects.show', $project) }}" 
                                       class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                                        <i class="fas fa-eye mr-1"></i>
                                        Voir
                                    </a>
                                    @if($project->user_id === auth()->id())
                                        <a href="#" 
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300 transition-colors">
                                            <i class="fas fa-cog mr-1"></i>
                                            Paramètres
                                        </a>
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
                        </div>
                    @endforeach

                    {{-- Carte d'ajout de projet --}}
                    <div class="project-card border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-blue-500 dark:hover:border-blue-400 bg-gray-50 dark:bg-gray-800/50">
                        <a href="{{ route('projects.create') }}" class="block h-full">
                            <div class="p-6 text-center h-full flex flex-col justify-center">
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

                {{-- Message si aucun résultat de recherche --}}
                <div id="noResults" class="hidden text-center py-12">
                    <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-search text-gray-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                        Aucun projet trouvé
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        Essayez de modifier vos termes de recherche ou vos filtres.
                    </p>
                </div>

            @else
                {{-- État vide --}}
                <div class="text-center py-16">
                    <div class="mx-auto w-32 h-32 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mb-8">
                        <i class="fas fa-rocket text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                        Aucun projet pour le moment
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">
                        Créez votre premier projet et commencez à organiser vos tâches avec la méthode Kanban.
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

    @push('scripts')
        <script>
            // Fonctionnalité de recherche
            function projectSearch() {
                return {
                    searchTerm: '',
                    
                    filterProjects() {
                        const searchTerm = this.searchTerm.toLowerCase();
                        const projects = document.querySelectorAll('.project-item');
                        const noResults = document.getElementById('noResults');
                        let visibleCount = 0;
                        
                        projects.forEach(project => {
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
                        } else {
                            noResults.classList.add('hidden');
                        }
                    }
                }
            }
            
            // Fonctionnalité de filtrage
            function projectFilters() {
                return {
                    currentFilter: 'all',
                    
                    setFilter(filter) {
                        this.currentFilter = filter;
                        this.applyFilter();
                    },
                    
                    applyFilter() {
                        const projects = document.querySelectorAll('.project-item');
                        
                        projects.forEach(project => {
                            const role = project.getAttribute('data-project-role');
                            
                            if (this.currentFilter === 'all' || 
                                (this.currentFilter === 'owner' && role === 'owner') ||
                                (this.currentFilter === 'member' && role === 'member')) {
                                project.classList.remove('project-hidden');
                            } else {
                                project.classList.add('project-hidden');
                            }
                        });
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
