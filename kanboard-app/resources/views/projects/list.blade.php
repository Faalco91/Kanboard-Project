<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ $project->name }} - Vue Liste
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ $tasks->total() }} tâches au total
                </p>
            </div>
            
            {{-- Navigation des vues --}}
            <nav class="flex flex-col sm:flex-row w-full sm:w-auto gap-2 sm:gap-4">
                <a href="{{ route('projects.show', $project) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium text-center text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors">
                    <i class="fas fa-columns mr-2"></i>Kanban
                </a>
                
                <a href="{{ route('projects.list', $project) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium text-center bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                    <i class="fas fa-list mr-2"></i>Liste
                </a>
                
                <a href="{{ route('projects.calendar', $project) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium text-center text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors">
                    <i class="fas fa-calendar mr-2"></i>Calendrier
                </a>
            </nav>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Filtres et recherche --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Recherche --}}
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Recherche
                        </label>
                        <input type="text" 
                               name="search" 
                               id="search"
                               value="{{ request('search') }}"
                               placeholder="Rechercher une tâche..."
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100">
                    </div>

                    {{-- Filtre par statut --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Statut
                        </label>
                        <select name="status" 
                                id="status"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100">
                            <option value="">Tous les statuts</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filtre par catégorie --}}
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Catégorie
                        </label>
                        <select name="category" 
                                id="category"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100">
                            <option value="">Toutes les catégories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Boutons --}}
                    <div class="flex items-end space-x-2">
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-search mr-2"></i>Filtrer
                        </button>
                        <a href="{{ route('projects.list', $project) }}" 
                           class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors">
                            <i class="fas fa-times mr-2"></i>Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Liste des tâches --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                
                {{-- En-tête du tableau --}}
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                    <div class="grid grid-cols-12 gap-4 items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                        <div class="col-span-4">
                            <a href="{{ route('projects.list', $project) }}?{{ http_build_query(array_merge(request()->query(), ['sort' => 'title', 'order' => request('order') === 'asc' ? 'desc' : 'asc'])) }}" 
                               class="flex items-center hover:text-blue-600 dark:hover:text-blue-400">
                                Tâche
                                @if(request('sort') === 'title')
                                    <i class="fas fa-chevron-{{ request('order') === 'asc' ? 'up' : 'down' }} ml-1 text-xs"></i>
                                @endif
                            </a>
                        </div>
                        <div class="col-span-2">
                            <a href="{{ route('projects.list', $project) }}?{{ http_build_query(array_merge(request()->query(), ['sort' => 'column', 'order' => request('order') === 'asc' ? 'desc' : 'asc'])) }}" 
                               class="flex items-center hover:text-blue-600 dark:hover:text-blue-400">
                                Statut
                                @if(request('sort') === 'column')
                                    <i class="fas fa-chevron-{{ request('order') === 'asc' ? 'up' : 'down' }} ml-1 text-xs"></i>
                                @endif
                            </a>
                        </div>
                        <div class="col-span-2">Catégorie</div>
                        <div class="col-span-2">Assigné à</div>
                        <div class="col-span-1">
                            <a href="{{ route('projects.list', $project) }}?{{ http_build_query(array_merge(request()->query(), ['sort' => 'due_date', 'order' => request('order') === 'asc' ? 'desc' : 'asc'])) }}" 
                               class="flex items-center hover:text-blue-600 dark:hover:text-blue-400">
                                Échéance
                                @if(request('sort') === 'due_date')
                                    <i class="fas fa-chevron-{{ request('order') === 'asc' ? 'up' : 'down' }} ml-1 text-xs"></i>
                                @endif
                            </a>
                        </div>
                        <div class="col-span-1">Actions</div>
                    </div>
                </div>

                {{-- Corps du tableau --}}
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($tasks as $task)
                        <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="grid grid-cols-12 gap-4 items-center">
                                {{-- Titre de la tâche --}}
                                <div class="col-span-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-1">
                                            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $task->title }}
                                            </h3>
                                            @if($task->description)
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                                                    {{ Str::limit($task->description, 100) }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Statut --}}
                                <div class="col-span-2">
                                    @php
                                        $statusColors = [
                                            'Backlog' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                            'To Do' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                            'In Progress' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                            'To Be Checked' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                            'Done' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$task->column] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $task->column }}
                                    </span>
                                </div>

                                {{-- Catégorie --}}
                                <div class="col-span-2">
                                    @if($task->category)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white" 
                                              style="background-color: {{ $task->color ?? '#6b7280' }}">
                                            {{ $task->category }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </div>

                                {{-- Utilisateur assigné --}}
                                <div class="col-span-2">
                                    @if($task->user)
                                        <div class="flex items-center">
                                            <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center mr-2">
                                                <span class="text-xs font-semibold text-white">
                                                    {{ strtoupper(substr($task->user->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                                {{ $task->user->name }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400 dark:text-gray-500">Non assigné</span>
                                    @endif
                                </div>

                                {{-- Date d'échéance --}}
                                <div class="col-span-1">
                                    @if($task->due_date)
                                        @php
                                            $isOverdue = $task->due_date->isPast() && $task->column !== 'Done';
                                            $isDueSoon = $task->due_date->diffInDays(now()) <= 3 && !$task->due_date->isPast();
                                        @endphp
                                        <span class="text-xs {{ $isOverdue ? 'text-red-600 dark:text-red-400 font-semibold' : ($isDueSoon ? 'text-yellow-600 dark:text-yellow-400 font-semibold' : 'text-gray-500 dark:text-gray-400') }}">
                                            {{ $task->due_date->format('d/m/Y') }}
                                            @if($isOverdue)
                                                <i class="fas fa-exclamation-triangle ml-1"></i>
                                            @elseif($isDueSoon)
                                                <i class="fas fa-clock ml-1"></i>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </div>

                                {{-- Actions --}}
                                <div class="col-span-1">
                                    <div class="flex items-center space-x-2">
                                        <button onclick="ListTaskManager.editTask('{{ $task->id }}')" 
                                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                                title="Modifier">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        <button onclick="ListTaskManager.deleteTask('{{ $task->id }}')" 
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                                title="Supprimer">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-tasks text-gray-400 text-xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                Aucune tâche trouvée
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">
                                @if(request()->hasAny(['search', 'status', 'category']))
                                    Aucune tâche ne correspond à vos critères de recherche.
                                @else
                                    Ce projet ne contient pas encore de tâches.
                                @endif
                            </p>
                            <a href="{{ route('projects.show', $project) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Créer une tâche
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Pagination --}}
            @if($tasks->hasPages())
                <div class="mt-6">
                    {{ $tasks->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal de tâche --}}
    <div id="taskModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    <span id="modalTitle">Modifier la tâche</span>
                </h3>
            </div>
            
            <form id="taskForm" class="px-6 py-4 space-y-4">
                <input type="hidden" id="taskMode" value="edit">
                <input type="hidden" id="editTargetId" value="">
                
                <div>
                    <label for="taskTitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Titre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="taskTitle" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100">
                </div>

                <div>
                    <label for="taskDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea id="taskDescription" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"></textarea>
                </div>
                
                <div>
                    <label for="taskCategory" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Catégorie
                    </label>
                    <input type="text" 
                           id="taskCategory"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100">
                </div>

                <div>
                    <label for="taskPriority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Priorité
                    </label>
                    <select id="taskPriority" 
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100">
                        <option value="low">🟢 Faible</option>
                        <option value="medium" selected>🟡 Moyenne</option>
                        <option value="high">🟠 Haute</option>
                        <option value="urgent">🔴 Urgente</option>
                    </select>
                </div>
                
                <div>
                    <label for="taskColor" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Couleur
                    </label>
                    <input type="color" 
                           id="taskColor" 
                           value="#3b82f6"
                           class="w-12 h-10 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer">
                </div>
                
                <div>
                    <label for="taskDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Date d'échéance
                    </label>
                    <input type="date" 
                           id="taskDate"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100">
                </div>
            </form>
            
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button type="button" 
                        id="cancelTaskModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Annuler
                </button>
                <button type="submit" 
                        form="taskForm"
                        id="saveTaskBtn"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                    Sauvegarder
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Script spécifique à la vue liste
        window.ListTaskManager = (function() {
            'use strict';
            
            console.log('Initialisation du gestionnaire de tâches pour la vue liste');
            
            // Désactiver task.js si présent
            if (window.taskSystemLoaded) {
                console.log('Désactivation de task.js pour la vue liste');
                window.taskSystemInitialized = false;
            }
            
            const projectId = {{ $project->id }};
            
            // Fonction pour éditer une tâche
            function editTask(taskId) {
                console.log('Édition de la tâche:', taskId);
                
                fetch(`/tasks/${taskId}/data`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                })
                .then(response => {
                    console.log('Statut réponse:', response.status);
                    if (!response.ok) {
                        return response.text().then(text => {
                            console.error('Réponse serveur:', text);
                            throw new Error(`HTTP ${response.status}: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(task => {
                    console.log('Données reçues:', task);
                    
                    // Remplir le formulaire
                    document.getElementById('taskMode').value = 'edit';
                    document.getElementById('editTargetId').value = taskId;
                    document.getElementById('taskTitle').value = task.title || '';
                    document.getElementById('taskDescription').value = task.description || '';
                    document.getElementById('taskCategory').value = task.category || '';
                    document.getElementById('taskPriority').value = task.priority || 'medium';
                    document.getElementById('taskColor').value = task.color || '#3b82f6';
                    if (task.due_date) {
                        // Si c'est déjà au bon format (YYYY-MM-DD)
                        if (task.due_date.match(/^\d{4}-\d{2}-\d{2}$/)) {
                            document.getElementById('taskDate').value = task.due_date;
                        } else {
                            // Sinon, parser et reformater
                            const date = new Date(task.due_date);
                            if (!isNaN(date.getTime())) {
                                document.getElementById('taskDate').value = date.toISOString().split('T')[0];
                            }
                        }
                        console.log('📅 Date formatée:', document.getElementById('taskDate').value);
                    } else {
                        document.getElementById('taskDate').value = '';
                    }
                    
                    // Afficher le modal
                    document.getElementById('taskModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Erreur lors du chargement:', error);
                    showNotification('Erreur lors du chargement de la tâche: ' + error.message, 'error');
                });
            }
            
            // Fonction pour supprimer une tâche
            function deleteTask(taskId) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')) {
                    return;
                }
                
                console.log('Suppression de la tâche:', taskId);
                
                fetch(`/tasks/${taskId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                })
                .then(response => {
                    console.log('📡 Statut réponse suppression:', response.status);
                    if (!response.ok) {
                        return response.text().then(text => {
                            console.error('Réponse serveur:', text);
                            throw new Error(`HTTP ${response.status}: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Réponse suppression:', data);
                    
                    if (data.success) {
                        showNotification('Tâche supprimée avec succès', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        throw new Error(data.message || 'Erreur inconnue');
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la suppression:', error);
                    showNotification('Erreur lors de la suppression: ' + error.message, 'error');
                });
            }
            
            // Fonction pour afficher les notifications
            function showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 p-4 rounded-lg text-white z-50 transition-all duration-300 ${
                    type === 'success' ? 'bg-green-500' : 
                    type === 'error' ? 'bg-red-500' : 'bg-blue-500'
                }`;
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                notification.textContent = message;
                
                document.body.appendChild(notification);
                
                // Animation d'entrée
                setTimeout(() => {
                    notification.style.opacity = '1';
                    notification.style.transform = 'translateX(0)';
                }, 100);
                
                // Suppression après 3 secondes
                setTimeout(() => {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }
            
            // Initialisation au chargement du DOM
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Initialisation des gestionnaires d\'événements');
                
                // Gestionnaire pour fermer le modal
                const cancelBtn = document.getElementById('cancelTaskModal');
                const taskModal = document.getElementById('taskModal');
                
                if (cancelBtn) {
                    cancelBtn.addEventListener('click', () => {
                        taskModal.classList.add('hidden');
                    });
                }
                
                // Fermer le modal en cliquant en dehors
                if (taskModal) {
                    taskModal.addEventListener('click', (e) => {
                        if (e.target === e.currentTarget) {
                            taskModal.classList.add('hidden');
                        }
                    });
                }
                
                // Gestionnaire pour la soumission du formulaire
                const taskForm = document.getElementById('taskForm');
                if (taskForm) {
                    taskForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        const taskId = document.getElementById('editTargetId').value;
                        const saveBtn = document.getElementById('saveTaskBtn');
                        
                        // Éviter les double-clics
                        if (saveBtn.disabled) return;
                        
                        // Validation côté client
                        const title = document.getElementById('taskTitle').value.trim();
                        if (!title) {
                            showNotification('Le titre est obligatoire', 'error');
                            return;
                        }
                        
                        // Préparer les données avec TOUS les champs
                        const formData = {
                            title: title,
                            description: document.getElementById('taskDescription').value.trim(),
                            category: document.getElementById('taskCategory').value.trim(),
                            priority: document.getElementById('taskPriority').value,
                            color: document.getElementById('taskColor').value,
                            due_date: document.getElementById('taskDate').value || null
                        };
                        
                        console.log('Mise à jour avec données:', formData);
                        
                        // Désactiver le bouton
                        saveBtn.disabled = true;
                        saveBtn.textContent = 'Sauvegarde...';
                        
                        fetch(`/tasks/${taskId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            },
                            body: JSON.stringify(formData)
                        })
                        .then(response => {
                            console.log('📡 Statut réponse mise à jour:', response.status);
                            if (!response.ok) {
                                return response.text().then(text => {
                                    console.error('Réponse serveur:', text);
                                    let errorMessage = `HTTP ${response.status}`;
                                    try {
                                        const errorData = JSON.parse(text);
                                        errorMessage = errorData.message || errorData.error || errorMessage;
                                    } catch(e) {
                                        errorMessage = text || errorMessage;
                                    }
                                    throw new Error(errorMessage);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Tâche mise à jour:', data);
                            
                            // Fermer le modal
                            document.getElementById('taskModal').classList.add('hidden');
                            
                            // Afficher notification de succès
                            showNotification('Tâche mise à jour avec succès', 'success');
                            
                            // Recharger la page
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        })
                        .catch(error => {
                            console.error('Erreur lors de la mise à jour:', error);
                            showNotification('Erreur lors de la mise à jour: ' + error.message, 'error');
                        })
                        .finally(() => {
                            // Réactiver le bouton
                            saveBtn.disabled = false;
                            saveBtn.textContent = 'Sauvegarder';
                        });
                    });
                }
                
                console.log('Gestionnaire de tâches pour la vue liste initialisé');
            });
            
            // API publique
            return {
                editTask: editTask,
                deleteTask: deleteTask,
                showNotification: showNotification
            };
            
        })();
    </script>
    @endpush
</x-app-layout>
