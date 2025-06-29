<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $project->name }}
            </h2>
            <div class="flex items-center space-x-4">
                <nav class="flex space-x-4">
                    <a href="{{ route('projects.show', $project) }}" 
                       class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('projects.show') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        Tableau Kanban
                    </a>
                    <a href="{{ route('project.members.index', $project) }}" 
                       class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('project.members.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        Membres
                    </a>
                </nav>
                <a href="{{ route('projects.export-ical', $project) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Export iCal
                </a>
            </div>
        </div>
    </x-slot>

    @vite(['resources/css/show.css'])
    @vite(['resources/css/task.css'])

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid-kanban">
                @foreach(['Backlog', 'To Do', 'In Progress', 'To Be Checked', 'Done'] as $column)
                    <div class="kanban-column-container">
                        <div class="kanban-header">
                            <h3>{{ $column }}</h3>
                            <div class="kanban-menu">⋯</div>
                        </div>

                        <ul class="kanban-column" id="column-{{ Str::slug($column) }}" data-column="{{ $column }}">
                            {{-- Les cartes iront ici --}}
                        </ul>

                        <button class="add-task-btn" data-column="{{ $column }}">
                            + Ajouter une carte
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal personnalisée -->
    <div id="taskModal" class="modal hidden">
        <div class="modal-content">
            <h2 id="taskModalTitle">Nouvelle tâche</h2>

            <form id="taskForm">
                <div class="form-group">
                    <label for="taskTitle">Titre de la tâche</label>
                    <input type="text" id="taskTitle" name="taskTitle" required>
                </div>

                <div class="form-group">
                    <label for="taskCategory">Catégorie / rôle</label>
                    <input type="text" id="taskCategory" name="taskCategory" placeholder="Ex : DevOps, Frontend..." required>
                </div>

                <div class="form-group">
                    <label for="taskColor">Couleur du badge (code hex)</label>
                    <input type="color" id="taskColor" name="taskColor">
                </div>

                <input type="hidden" id="taskColumn" name="taskColumn">
                <input type="hidden" id="taskMode" name="taskMode" value="create">
                <input type="hidden" id="editTargetId">

                <div class="modal-buttons">
                    <button type="button" id="cancelTaskModal" class="cancel">Annuler</button>
                    <button type="submit" class="submit">Valider</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const projectId = {{ $project->id }}; // Déclaration de l'ID du projet accessible à l'echelle globale de la page
        const tasks = @json($tasks); //transmission des données déjà récupérer en php,issues de la table tasks dans la bdd, à une variable js sous format json. Cela permet de ne pas avoir à les récuperer à chaque fois via une methode fetch
        const userInitials = @json($userInitials); // meme fonctionnement que pour tasks, mais pour récupérer la première lettre du nom de l'utilisateur connecté
    </script>
</x-app-layout>
