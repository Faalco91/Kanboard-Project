<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $project->name }} - Liste des tâches
            </h2>
            <nav class="flex space-x-4">
                <a href="{{ route('projects.show', $project) }}" 
                   class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('projects.show') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    Tableau Kanban
                </a>
                <a href="{{ route('projects.list', $project) }}" 
                   class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('projects.list') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    Liste
                </a>
                <a href="{{ route('projects.calendar', $project) }}" 
                   class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('projects.calendar') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    Calendrier
                </a>
                <a href="{{ route('project.members.index', $project) }}" 
                   class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('project.members.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    Membres
                </a>
            </nav>
        </div>
    </x-slot>

    @vite(['resources/css/list.css'])

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Barre de recherche et filtres -->
            <div class="search-filters">
                <h3>🔍 Rechercher et filtrer les tâches</h3>
                <form method="GET" action="{{ route('projects.list', $project) }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Recherche simple -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-white mb-1">Rechercher</label>
                            <input type="text" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Titre ou catégorie..."
                                   class="search-input w-full">
                        </div>

                        <!-- Filtre par colonne -->
                        <div>
                            <label for="column" class="block text-sm font-medium text-white mb-1">Colonne</label>
                            <select id="column" name="column" class="filter-select w-full">
                                <option value="">Toutes les colonnes</option>
                                @foreach($columns as $column)
                                    <option value="{{ $column }}" {{ request('column') == $column ? 'selected' : '' }}>
                                        {{ $column }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtre par catégorie -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-white mb-1">Catégorie</label>
                            <select id="category" name="category" class="filter-select w-full">
                                <option value="">Toutes les catégories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tri -->
                        <div>
                            <label for="sort_by" class="block text-sm font-medium text-white mb-1">Trier par</label>
                            <select id="sort_by" name="sort_by" class="filter-select w-full">
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date de création</option>
                                <option value="title" {{ request('sort_by') == 'title' ? 'selected' : '' }}>Titre</option>
                                <option value="category" {{ request('sort_by') == 'category' ? 'selected' : '' }}>Catégorie</option>
                                <option value="column" {{ request('sort_by') == 'column' ? 'selected' : '' }}>Colonne</option>
                                <option value="due_date" {{ request('sort_by') == 'due_date' ? 'selected' : '' }}>Date d'échéance</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <div class="flex space-x-2">
                            <button type="submit" class="filter-button">
                                🔍 Filtrer
                            </button>
                            <a href="{{ route('projects.list', $project) }}" class="reset-button">
                                🔄 Réinitialiser
                            </a>
                        </div>
                        <div class="text-white text-sm">
                            📊 {{ $tasks->count() }} tâche(s) trouvée(s)
                        </div>
                    </div>
                </form>
            </div>

            <!-- Liste des tâches -->
            <div class="task-table">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    📋 Tâche
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    🏷️ Catégorie
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    📁 Colonne
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    📅 Date d'échéance
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    🕒 Date de création
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ⚙️ Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tasks as $task)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-blue-600">{{ $userInitials }}</span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($task->category)
                                            <span class="category-badge" 
                                                  style="background-color: {{ $task->color ?? '#3B82F6' }}20; color: {{ $task->color ?? '#3B82F6' }};">
                                                {{ $task->category }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="column-badge">
                                            {{ $task->column }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($task->due_date)
                                            {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $task->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="editTask({{ $task->id }})" 
                                                class="action-button edit-button mr-2">
                                            ✏️ Modifier
                                        </button>
                                        <button onclick="deleteTask({{ $task->id }})" 
                                                class="action-button delete-button">
                                            🗑️ Supprimer
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        🚫 Aucune tâche trouvée
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour éditer une tâche -->
    <div id="taskModal" class="modal hidden">
        <div class="modal-content">
            <h2 id="taskModalTitle">Modifier la tâche</h2>
            <form id="taskForm">
                <div class="form-group">
                    <label for="taskTitle">Titre de la tâche</label>
                    <input type="text" id="taskTitle" name="taskTitle" required>
                </div>
                <div class="form-group">
                    <label for="taskCategory">Catégorie</label>
                    <input type="text" id="taskCategory" name="taskCategory">
                </div>
                <div class="form-group">
                    <label for="taskColor">Couleur</label>
                    <input type="color" id="taskColor" name="taskColor" value="#3B82F6">
                </div>
                <div class="form-group">
                    <label for="taskColumn">Colonne</label>
                    <select id="taskColumn" name="taskColumn" required>
                        <option value="Backlog">Backlog</option>
                        <option value="To Do">To Do</option>
                        <option value="In Progress">In Progress</option>
                        <option value="To Be Checked">To Be Checked</option>
                        <option value="Done">Done</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="taskDate">Date d'échéance</label>
                    <input type="date" id="taskDate" name="taskDate">
                </div>
                <input type="hidden" id="taskMode" name="taskMode" value="edit">
                <input type="hidden" id="editTargetId">
                <div class="modal-buttons">
                    <button type="button" id="cancelTaskModal" class="cancel">Annuler</button>
                    <button type="submit" class="submit">Valider</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const projectId = {{ $project->id }};
        const tasks = @json($tasks);
        const userInitials = @json($userInitials);

        function editTask(taskId) {
            const task = tasks.find(t => t.id === parseInt(taskId));
            
            if (!task) {
                return;
            }
            
            const modal = document.getElementById('taskModal');
            const modeInput = document.getElementById('taskMode');
            const editTargetInput = document.getElementById('editTargetId');
            
            modeInput.value = 'edit';
            editTargetInput.value = taskId;
            document.getElementById('taskModalTitle').textContent = 'Modifier la tâche';
            
            // Remplir le formulaire
            document.getElementById('taskTitle').value = task.title;
            document.getElementById('taskCategory').value = task.category || '';
            document.getElementById('taskColor').value = task.color || '#3B82F6';
            document.getElementById('taskColumn').value = task.column;
            
            // Formater la date correctement pour l'input date
            if (task.due_date) {
                const date = new Date(task.due_date);
                const formattedDate = date.toISOString().split('T')[0]; // Format YYYY-MM-DD
                document.getElementById('taskDate').value = formattedDate;
            } else {
                document.getElementById('taskDate').value = '';
            }
            
            modal.classList.remove('hidden');
        }

        function deleteTask(taskId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')) {
                fetch(`/tasks/${taskId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).then(response => {
                    if (response.ok) {
                        window.location.reload();
                    }
                });
            }
        }

        // Gestion du modal
        document.getElementById('cancelTaskModal').addEventListener('click', () => {
            document.getElementById('taskModal').classList.add('hidden');
        });

        document.getElementById('taskModal').addEventListener('click', (e) => {
            if (e.target.id === 'taskModal') {
                e.target.classList.add('hidden');
            }
        });

        document.getElementById('taskForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const mode = formData.get('taskMode');
            
            const taskData = {
                title: formData.get('taskTitle'),
                category: formData.get('taskCategory'),
                color: formData.get('taskColor'),
                column: formData.get('taskColumn'),
                due_date: formData.get('taskDate'),
                project_id: projectId
            };
            
            try {
                if (mode === 'edit') {
                    const taskId = document.getElementById('editTargetId').value;
                    
                    if (!taskId || isNaN(taskId)) {
                        alert('Erreur: ID de tâche invalide');
                        return;
                    }
                    
                    const response = await fetch(`/tasks/${taskId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(taskData)
                    });
                    
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        alert('Erreur lors de la modification de la tâche');
                    }
                }
                
                document.getElementById('taskModal').classList.add('hidden');
            } catch (error) {
                alert('Erreur lors de la sauvegarde: ' + error.message);
            }
        });
    </script>
</x-app-layout> 