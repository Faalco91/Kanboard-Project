<x-app-layout>
    <x-slot name="header">
        <h2 class="page-title">
            Projet : {{ $project->name }}
        </h2>
    </x-slot>

    @vite(['resources/css/show.css'])
    @vite(['resources/css/task.css'])

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
