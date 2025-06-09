<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @vite(['resources/css/dashboard.css'])
    
    <div class="section">
        <div class="card">
            <div class="card-text">
                {{ __("You're logged in!") }}
            </div>
        </div>

        <h1 class="page-title">
            {{ __("Vos projets") }}
        </h1>

        <div class="card">
            @if($projects->isEmpty())
                <div class="card-text">
                    {{ __("Vous n'avez pas de projets") }}
                </div>
            @else
                <ul class="project-list">
                    @foreach($projects as $project)
                        <li class="project-item">
                            <a href="{{ route('projects.show', $project->id) }}" class="project-link">
                                {{ $project->name }}
                            </a>
                            <p class="project-desc">
                                {{ $project->description ?? 'Aucune description' }}
                            </p>
                        </li>
                    @endforeach
                </ul>
            @endif

            <button class="btn" id="openCreateModal">
                {{ __("Créer un projet") }}
            </button>
        </div>
    </div>

    <!-- Modal pour la création d'un projet -->
    <div id="createProjectModal" class="modal hidden">
        <div class="modal-content">
            <h2>Créer un projet</h2>

            <form method="POST" action="{{ route('projects.index') }}">
                @csrf
                <div class="form-group">
                    <label for="name">Nom du projet</label>
                    <input type="text" name="name" id="name" required>
                </div>

                <div class="modal-buttons">
                    <button type="button" id="cancelModalBtn" class="cancel">Annuler</button>
                    <button type="submit" class="submit">Créer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script JS pour gérer l'ouverture/fermeture de la modal -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const openBtn = document.getElementById('openCreateModal');
            const modal = document.getElementById('createProjectModal');
            const cancelBtn = document.getElementById('cancelModalBtn');

            openBtn.addEventListener('click', () => modal.classList.remove('hidden'));
            cancelBtn.addEventListener('click', () => modal.classList.add('hidden'));

            // Ferme la modal si on clique en dehors du formulaire
            modal.addEventListener('click', (e) => {
                if (e.target === modal) modal.classList.add('hidden');
            });
        });
    </script>
</x-app-layout>
