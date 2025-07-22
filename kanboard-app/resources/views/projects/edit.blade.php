<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold leading-tight text-gray-800 dark:text-gray-200">
                    Modifier le projet
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Paramètres et configuration de {{ $project->name }}
                </p>
            </div>
            
            <div class="flex gap-3">
                <a href="{{ route('projects.show', $project) }}" 
                   class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    Retour au projet
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Messages de succès/erreur --}}
            @if(session('success'))
                <div class="mb-6 bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <div class="flex">
                        <i class="fas fa-check-circle text-green-400 mr-3 mt-0.5"></i>
                        <p class="text-green-800 dark:text-green-200">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <div class="flex">
                        <i class="fas fa-exclamation-circle text-red-400 mr-3 mt-0.5"></i>
                        <div>
                            <p class="text-red-800 dark:text-red-200 font-medium mb-2">Erreurs de validation :</p>
                            <ul class="text-red-700 dark:text-red-300 list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Formulaire d'édition --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                
                {{-- En-tête --}}
<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
        <i class="fas fa-cog mr-2 text-gray-500 dark:text-gray-400"></i>
        Informations du projet
    </h3>
    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
        Modifiez les détails de votre projet Kanban
    </p>
</div>

                {{-- Formulaire --}}
                <form action="{{ route('projects.update', $project) }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        {{-- Nom du projet--}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-white mb-2">
                                Nom du projet <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $project->name) }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors"
                                   placeholder="Entrez le nom de votre projet">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-white mb-2">
                                Description
                            </label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors"
                                      placeholder="Décrivez votre projet (optionnel)">{{ old('description', $project->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">
                                Maximum 1000 caractères
                            </p>
                        </div>

                        {{-- Informations supplémentaires --}}
                        <div class="bg-blue-50 dark:bg-gray-700 border border-blue-200 dark:border-gray-600 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-blue-900 dark:text-white mb-3">
                                <i class="fas fa-info-circle mr-2 text-blue-600 dark:text-blue-400"></i>
                                Informations du projet
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-blue-700 dark:text-gray-200">Créé le :</span>
                                    <span class="text-blue-900 dark:text-white ml-2 font-medium">
                                        {{ $project->created_at->format('d/m/Y à H:i') }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-blue-700 dark:text-gray-200">Dernière modification :</span>
                                    <span class="text-blue-900 dark:text-white ml-2 font-medium">
                                        {{ $project->updated_at->format('d/m/Y à H:i') }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-blue-700 dark:text-gray-200">Nombre de tâches :</span>
                                    <span class="text-blue-900 dark:text-white ml-2 font-medium">
                                        {{ $project->tasks()->count() }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-blue-700 dark:text-gray-200">Membres :</span>
                                    <span class="text-blue-900 dark:text-white ml-2 font-medium">
                                        {{ $project->members()->count() + 1 }} {{-- +1 pour le propriétaire --}}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Boutons d'action --}}
                    <div class="flex flex-col sm:flex-row gap-3 justify-between items-start sm:items-center mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex gap-3">
                            <button type="submit" 
                                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="fas fa-save"></i>
                                Sauvegarder les modifications
                            </button>
                            
                            <a href="{{ route('projects.show', $project) }}" 
                               class="inline-flex items-center gap-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-medium py-2.5 px-6 rounded-lg transition-colors">
                                <i class="fas fa-times"></i>
                                Annuler
                            </a>
                        </div>

                        {{-- Bouton de suppression --}}
                        <div>
                            <button type="button" 
                                    onclick="confirmDelete()"
                                    class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-red-500">
                                <i class="fas fa-trash"></i>
                                Supprimer le projet
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Actions rapides --}}
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- Gestion des membres --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 ml-3">
                            Membres
                        </h4>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Gérez les membres de votre équipe projet
                    </p>
                    <a href="{{ route('project.members.index', $project) }}" 
                       class="inline-flex items-center gap-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium transition-colors">
                        Gérer les membres
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                {{-- Statistiques --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-bar text-green-600 dark:text-green-400"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 ml-3">
                            Statistiques
                        </h4>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Analysez les performances de votre projet
                    </p>
                    <a href="{{ route('projects.stats', $project) }}" 
                       class="inline-flex items-center gap-2 text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 font-medium transition-colors">
                        Voir les statistiques
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                {{-- Export --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-download text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 ml-3">
                            Export
                        </h4>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Exportez vos tâches au format iCal
                    </p>
                    <a href="{{ route('projects.export-ical', $project) }}" 
                       class="inline-flex items-center gap-2 text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 font-medium transition-colors">
                        Télécharger iCal
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de confirmation de suppression --}}
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                    Confirmer la suppression
                </h3>
            </div>
            
            <div class="px-6 py-4">
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Êtes-vous sûr de vouloir supprimer le projet <strong class="text-gray-900 dark:text-gray-100">"{{ $project->name }}"</strong> ?
                </p>
                <p class="text-red-600 dark:text-red-400 text-sm">
                    <i class="fas fa-warning mr-1"></i>
                    Cette action est irréversible et supprimera également toutes les tâches associées.
                </p>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex gap-3 justify-end">
                <button type="button" 
                        onclick="closeDeleteModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Annuler
                </button>
                
                <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                        <i class="fas fa-trash mr-2"></i>
                        Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Amélioration du contraste pour le dark mode */
            .dark input::placeholder,
            .dark textarea::placeholder {
                color: #9ca3af !important;
            }
            
            .dark input,
            .dark textarea {
                color: white !important;
                background-color: #374151 !important;
            }
            
            .dark input:focus,
            .dark textarea:focus {
                background-color: #374151 !important;
                border-color: #3b82f6 !important;
                color: white !important;
            }
            
            /* Amélioration des cartes d'action */
            .dark .hover\:shadow-md:hover {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2);
            }

            /* Force la couleur du texte en dark mode */
            .dark label {
                color: white !important;
            }

            .dark h3 {
                color: white !important;
            }

            .dark p {
                color: #d1d5db !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function confirmDelete() {
                document.getElementById('deleteModal').classList.remove('hidden');
            }
            
            function closeDeleteModal() {
                document.getElementById('deleteModal').classList.add('hidden');
            }
            
            // Fermer le modal en cliquant en dehors
            document.getElementById('deleteModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeDeleteModal();
                }
            });
            
            // Fermer avec Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeDeleteModal();
                }
            });
        </script>
    @endpush
</x-app-layout>
