<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center space-x-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ $project->name }}
                </h2>
                <div class="flex items-center space-x-2">
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
            
            {{-- Navigation des vues --}}
            <nav class="flex flex-col sm:flex-row w-full sm:w-auto gap-2 sm:gap-4">
                <a href="{{ route('projects.show', $project) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium text-center bg-blue-600 text-white hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-columns mr-2"></i>Kanban
                </a>
                
                {{-- Vue Liste --}}
                <a href="{{ route('projects.list', $project) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium text-center text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-list mr-2"></i>Liste
                </a>
                
                {{-- Vue Calendrier --}}
                <a href="{{ route('projects.calendar', $project) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium text-center text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-calendar mr-2"></i>Calendrier
                </a>
                
                {{-- Gestion des membres (si propriétaire) --}}
                @if($project->user_id === auth()->id())
                    <a href="#" 
                       class="px-4 py-2 rounded-lg text-sm font-medium text-center text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-users mr-2"></i>Membres
                    </a>
                @endif
            </nav>
        </div>
    </x-slot>

    {{-- Styles spécifiques --}}
    @push('styles')
        <style>
            /* ===== KANBAN BOARD STYLES ===== */
.kanban-board {
    display: grid;
    grid-template-columns: repeat(5, minmax(300px, 1fr));
    gap: 1.5rem;
    padding: 1.5rem;
    overflow-x: auto;
    min-height: calc(100vh - 200px);
}

@media (max-width: 1024px) {
    .kanban-board {
        grid-template-columns: repeat(5, minmax(280px, 300px));
        padding: 1rem;
    }
}

/* ===== COLONNES KANBAN ===== */
.kanban-column {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    min-height: 500px;
    max-height: calc(100vh - 250px);
    transition: all 0.2s ease;
}

.dark .kanban-column {
    background: #1f2937;
    border-color: #374151;
}

.kanban-column:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.kanban-column-header {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
    border-radius: 0.75rem 0.75rem 0 0;
    flex-shrink: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dark .kanban-column-header {
    background: #111827;
    border-color: #374151;
}

.kanban-column-body {
    flex: 1;
    padding: 0.75rem;
    overflow-y: auto;
    list-style: none;
    margin: 0;
    min-height: 200px;
}

/* ===== TÂCHES KANBAN ===== */
.task-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 0.75rem;
    cursor: grab;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    position: relative;
    display: block; /* IMPORTANT pour éviter les chevauchements */
}

.task-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border-color: #d1d5db;
}

.task-card:active {
    cursor: grabbing;
}

.dark .task-card {
    background: #374151;
    border-color: #4b5563;
}

.dark .task-card:hover {
    border-color: #6b7280;
}

/* ===== CONTENU DES TÂCHES ===== */
.task-badge {
    display: inline-block;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.task-title {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    line-height: 1.25rem;
    word-wrap: break-word;
}

.dark .task-title {
    color: #f9fafb;
}

.task-user {
    font-size: 0.75rem;
    color: #6b7280;
    margin-bottom: 0.5rem;
}

.dark .task-user {
    color: #9ca3af;
}

.task-date {
    font-size: 0.75rem;
    color: #6b7280;
    margin-bottom: 0.5rem;
}

.dark .task-date {
    color: #9ca3af;
}

.task-actions {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    display: flex;
    gap: 0.25rem;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.task-card:hover .task-actions {
    opacity: 1;
}

.task-actions button {
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 0.25rem;
    border: none;
    background: rgba(255, 255, 255, 0.9);
    color: #6b7280;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
}

.task-actions button:hover {
    background: white;
    color: #374151;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.dark .task-actions button {
    background: rgba(31, 41, 55, 0.9);
    color: #9ca3af;
}

.dark .task-actions button:hover {
    background: #1f2937;
    color: #d1d5db;
}

/* ===== BOUTONS D'AJOUT ===== */
.add-task-btn {
    width: 100%;
    padding: 1rem;
    text-align: center;
    color: #6b7280;
    border: 2px dashed #d1d5db;
    background: none;
    cursor: pointer;
    transition: all 0.2s ease;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    margin-top: 0.5rem;
}

.add-task-btn:hover {
    border-color: #3b82f6;
    color: #3b82f6;
    background: #f0f9ff;
}

.add-task-btn:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

.dark .add-task-btn {
    border-color: #4b5563;
    color: #9ca3af;
}

.dark .add-task-btn:hover {
    border-color: #60a5fa;
    color: #60a5fa;
    background: #1e3a8a;
}

/* ===== ANIMATIONS DRAG & DROP ===== */
.sortable-ghost {
    opacity: 0.4;
    background: #f0f9ff;
    border: 2px dashed #3b82f6;
}

.sortable-chosen {
    cursor: grabbing;
    transform: scale(1.05);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    z-index: 1000;
}

.sortable-drag {
    opacity: 0.8;
    transform: rotate(5deg);
}

.task-enter {
    animation: taskSlideIn 0.3s ease-out;
}

.task-updated {
    animation: taskUpdated 0.3s ease-in-out;
}

@keyframes taskSlideIn {
    from {
        opacity: 0;
        transform: translateY(-10px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes taskUpdated {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
        background-color: rgba(59, 130, 246, 0.1);
    }
    100% {
        transform: scale(1);
    }
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .kanban-board {
        grid-template-columns: repeat(5, minmax(280px, 300px));
        gap: 1rem;
        padding: 1rem;
        scroll-snap-type: x mandatory;
    }
    
    .kanban-column {
        scroll-snap-align: start;
    }
    
    .task-card {
        padding: 0.75rem;
        margin-bottom: 0.5rem;
    }
    
    .task-actions {
        opacity: 1; /* Toujours visible sur mobile */
    }
}
        </style>
    @endpush

    <div class="py-6">
        <div class="max-w-full mx-auto">
            {{-- Board Kanban --}}
            <div class="kanban-board">
                @foreach(['Backlog', 'To Do', 'In Progress', 'To Be Checked', 'Done'] as $column)
                    <div class="kanban-column" data-column="{{ $column }}">
                        {{-- En-tête de colonne --}}
                        <div class="kanban-column-header">
                            <div class="flex items-center space-x-2">
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $column }}</h3>
                                <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-medium bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300 rounded-full column-counter">
                                    {{ $tasks->where('column', $column)->count() }}
                                </span>
                            </div>
                            
                            {{-- Menu de colonne --}}
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        class="p-1 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                
                                {{-- Dropdown menu --}}
                                <div x-show="open" 
                                     @click.away="open = false"
                                     x-transition
                                     class="absolute right-0 z-10 mt-2 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600">
                                    <div class="py-1">
                                        <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 add-task-btn-menu" 
                                                data-column="{{ $column }}">
                                            <i class="fas fa-plus mr-2"></i>Ajouter une tâche
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Corps de colonne --}}
                        <ul class="kanban-column-body" id="column-{{ Str::slug($column) }}" data-column="{{ $column }}">
                            {{-- Tâches existantes --}}
                            @foreach($tasks->where('column', $column) as $task)
                                <li class="task-card" data-task-id="{{ $task->id }}">
                                    {{-- Badge de catégorie --}}
                                    @if($task->category)
                                        <div class="task-badge" style="background-color: {{ $task->color ?? '#6b7280' }}">
                                            {{ $task->category }}
                                        </div>
                                    @endif
                                    
                                    {{-- Titre de la tâche --}}
                                    <div class="task-title">{{ $task->title }}</div>
                                    
                                    {{-- Utilisateur assigné --}}
                                    <div class="task-user">
                                        <i class="fas fa-user-circle mr-1"></i>
                                        {{ strtoupper(substr($task->user->name ?? 'Non assigné', 0, 2)) }}
                                    </div>
                                    
                                    {{-- Date d'échéance (si présente) --}}
                                    @if($task->due_date)
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-2 task-date">
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}
                                        </div>
                                    @endif
                                    
                                    {{-- Actions de la tâche --}}
                                    <div class="task-actions">
                                        <button onclick="editTask('{{ $task->id }}')" 
                                                title="Modifier"
                                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteTask('{{ $task->id }}')" 
                                                title="Supprimer"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        {{-- Bouton d'ajout de tâche --}}
                        <button class="add-task-btn" data-column="{{ $column }}">
                            <i class="fas fa-plus mr-2"></i>
                            Ajouter une tâche
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Modal de création/édition de tâche --}}
    <div id="taskModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md">
            {{-- En-tête du modal --}}
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    <span id="modalTitle">Nouvelle tâche</span>
                </h3>
            </div>
            
            {{-- Corps du modal --}}
            <form id="taskForm" class="px-6 py-4 space-y-4">
                <input type="hidden" id="taskMode" value="create">
                <input type="hidden" id="editTargetId" value="">
                <input type="hidden" id="taskColumn" value="">
                
                {{-- Titre --}}
                <div>
                    <label for="taskTitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Titre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="taskTitle" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                           placeholder="Nom de la tâche">
                </div>
                
                {{-- Catégorie --}}
                <div>
                    <label for="taskCategory" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Catégorie
                    </label>
                    <input type="text" 
                           id="taskCategory"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                           placeholder="Ex: Frontend, Backend, Design...">
                </div>
                
                {{-- Couleur --}}
                <div>
                    <label for="taskColor" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Couleur
                    </label>
                    <div class="flex items-center space-x-3">
                        <input type="color" 
                               id="taskColor" 
                               value="#3b82f6"
                               class="w-12 h-10 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer">
                        <div class="flex space-x-2">
                            <button type="button" onclick="setTaskColor('#ef4444')" class="w-6 h-6 bg-red-500 rounded-full border-2 border-white shadow-sm"></button>
                            <button type="button" onclick="setTaskColor('#3b82f6')" class="w-6 h-6 bg-blue-500 rounded-full border-2 border-white shadow-sm"></button>
                            <button type="button" onclick="setTaskColor('#10b981')" class="w-6 h-6 bg-green-500 rounded-full border-2 border-white shadow-sm"></button>
                            <button type="button" onclick="setTaskColor('#f59e0b')" class="w-6 h-6 bg-yellow-500 rounded-full border-2 border-white shadow-sm"></button>
                            <button type="button" onclick="setTaskColor('#8b5cf6')" class="w-6 h-6 bg-purple-500 rounded-full border-2 border-white shadow-sm"></button>
                        </div>
                    </div>
                </div>
                
                {{-- Date d'échéance --}}
                <div>
                    <label for="taskDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Date d'échéance
                    </label>
                    <input type="date" 
                           id="taskDate"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100">
                </div>
            </form>
            
            {{-- Pied du modal --}}
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button type="button" 
                        id="cancelTaskModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Annuler
                </button>
                <button type="submit" 
                        form="taskForm"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <span id="submitButtonText">Créer</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    @push('scripts')
        <script>
            // Configuration globale pour les tâches
            const projectId = {{ $project->id }};
            const tasks = @json($tasks);
            const userInitials = '{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}';
            
            // Fonction pour définir la couleur de la tâche
            function setTaskColor(color) {
                const taskColor = document.getElementById('taskColor');
                if (taskColor) {
                    taskColor.value = color;
                }
            }

            // Gestion des boutons d'ajout de tâche depuis le menu
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.add-task-btn-menu').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        
                        const taskModal = document.getElementById('taskModal');
                        const taskMode = document.getElementById('taskMode');
                        const taskTitle = document.getElementById('taskTitle');
                        const taskCategory = document.getElementById('taskCategory');
                        const taskColor = document.getElementById('taskColor');
                        const taskDate = document.getElementById('taskDate');
                        const taskColumn = document.getElementById('taskColumn');

                        if (taskModal && taskMode && taskTitle && taskCategory && taskColor && taskColumn) {
                            taskMode.value = 'create';
                            taskTitle.value = '';
                            taskCategory.value = '';
                            taskColor.value = '#3b82f6';
                            if (taskDate) taskDate.value = '';
                            taskColumn.value = btn.dataset.column;
                            
                            // Mettre à jour le titre du modal
                            const modalTitle = document.getElementById('modalTitle');
                            const submitButtonText = document.getElementById('submitButtonText');
                            
                            if (modalTitle) modalTitle.textContent = 'Nouvelle tâche';
                            if (submitButtonText) submitButtonText.textContent = 'Créer';
                            
                            taskModal.classList.remove('hidden');
                            taskTitle.focus();
                        }
                    });
                });
            });
        </script>
        
        {{-- Script principal des tâches --}}
        <script src="{{ asset('js/task.js') }}"></script>
    @endpush
</x-app-layout>
