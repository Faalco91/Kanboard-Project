<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Tableau de bord') }}
            </h2>
        </div>
    </x-slot>

    @vite(['resources/css/dashboard.css'])

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Bouton de création de projet avec styles inline -->
            <div class="mb-6 text-right">
                <button 
                    onclick="openCreateProjectModal()" 
                    style="
                        background-color: #10B981;
                        color: white;
                        padding: 0.75rem 1.5rem;
                        border-radius: 0.375rem;
                        font-weight: 600;
                        cursor: pointer;
                        display: inline-flex;
                        align-items: center;
                        gap: 0.5rem;
                        border: none;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                    "
                >
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('Nouveau Projet') }}
                </button>
            </div>

            @if($projects->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 text-center">
                        {{ __("Vous n'avez pas encore de projets.") }}
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($projects as $project)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                            <a href="{{ route('projects.show', $project->id) }}" class="block">
                                <div class="p-6">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="text-lg font-semibold">{{ $project->name }}</h3>
                                        <span class="text-sm px-2 py-1 rounded-full {{ $project->user_id === Auth::id() ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $project->user_id === Auth::id() ? 'Propriétaire' : 'Membre' }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-4">
                                        {{ Str::limit($project->description ?? 'Aucune description', 100) }}
                                    </p>
                                    <div class="flex justify-between items-center text-sm text-gray-500">
                                        <span>{{ $project->tasks_count ?? 0 }} tâches</span>
                                        <span>Créé le {{ $project->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @include('projects.create')

    <!-- Script pour s'assurer que la fonction est disponible -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Définir la fonction globalement si elle n'existe pas déjà
            if (typeof window.openCreateProjectModal !== 'function') {
                window.openCreateProjectModal = function() {
                    const modal = document.getElementById('create-project-modal');
                    if (modal) {
                        modal.style.display = 'block';
                    } else {
                        console.error('Modal not found');
                    }
                }
            }
        });
    </script>
</x-app-layout>
