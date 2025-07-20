<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ $project->name }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Vue Kanban • {{ $project->tasks()->count() }} tâches
                </p>
            </div>
            
            {{-- Navigation des vues --}}
            <nav class="flex flex-col sm:flex-row w-full sm:w-auto gap-2 sm:gap-4">
                {{-- Vue Kanban --}}
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

    <a href="{{ route('projects.export-ical', $project) }}" 
       target="_blank"
       class="px-4 py-2 rounded-lg text-sm font-medium text-center text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
       title="Exporter vers Google Calendar, Outlook, etc.">
        <i class="fas fa-download mr-2"></i>Export iCal
    </a>
                
                {{-- Gestion des membres (si propriétaire) --}}
                @if($project->user_id === auth()->id())
                    <a href="{{ route('project.members.index', $project) }}" 
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

.kanban-column-header {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
    background: #f8fafc;
    border-radius: 0.75rem 0.75rem 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 10;
}

.dark .kanban-column-header {
    background: #111827;
    border-color: #374151;
}

.kanban-column-title {
    font-weight: 600;
    font-size: 0.875rem;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.dark .kanban-column-title {
    color: #d1d5db;
}

.kanban-tasks-container {
    flex: 1;
    padding: 1rem;
    overflow-y: auto;
    min-height: 400px;
}

/* ===== CARTES DE TÂCHES ===== */
.kanban-task {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.dark .kanban-task {
    background: #374151;
    border-color: #4b5563;
    color: #f3f4f6;
}

.kanban-task:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 12px 0 rgba(59, 130, 246, 0.15);
    transform: translateY(-2px);
}

.kanban-task:last-child {
    margin-bottom: 0;
}

.kanban-task-title {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.5rem;
    line-height: 1.4;
}

.dark .kanban-task-title {
    color: #f9fafb;
}

.kanban-task-description {
    color: #6b7280;
    font-size: 0.875rem;
    line-height: 1.5;
    margin-bottom: 0.75rem;
}

.dark .kanban-task-description {
    color: #d1d5db;
}

.kanban-task-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.75rem;
    color: #6b7280;
}

.dark .kanban-task-meta {
    color: #9ca3af;
}

.kanban-task-assignee {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.kanban-task-due-date {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.kanban-task-due-date.overdue {
    color: #dc2626;
}

.dark .kanban-task-due-date.overdue {
    color: #f87171;
}

/* ===== BOUTONS AJOUTER TÂCHE ===== */
.add-task-btn {
    width: 100%;
    padding: 0.75rem;
    border: 2px dashed #d1d5db;
    border-radius: 0.5rem;
    background: transparent;
    color: #6b7280;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.add-task-btn:hover {
    border-color: #3b82f6;
    color: #3b82f6;
    background: #eff6ff;
}

.dark .add-task-btn {
    border-color: #4b5563;
    color: #9ca3af;
}

.dark .add-task-btn:hover {
    border-color: #3b82f6;
    color: #60a5fa;
    background: #1e3a8a;
}

/* ===== DRAG & DROP ===== */
.kanban-task.dragging {
    opacity: 0.5;
    transform: rotate(2deg);
    z-index: 1000;
    box-shadow: 0 8px 25px 0 rgba(0, 0, 0, 0.15);
}

.kanban-column.drag-over {
    border-color: #3b82f6;
    background: #eff6ff;
}

.dark .kanban-column.drag-over {
    border-color: #60a5fa;
    background: #1e3a8a;
}

/* ===== MODAL STYLES ===== */
.modal-overlay {
    position: fixed;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 50;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.modal-content {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
}

.dark .modal-content {
    background: #1f2937;
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.dark .modal-header {
    border-color: #374151;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    padding: 1.5rem;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
}

.dark .modal-footer {
    border-color: #374151;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .kanban-board {
        grid-template-columns: 1fr;
        padding: 0.75rem;
        gap: 1rem;
    }
    
    .kanban-column {
        min-height: 300px;
        max-height: none;
    }
    
    .modal-content {
        margin: 0.5rem;
        width: calc(100% - 1rem);
    }
}

/* ===== ANIMATIONS ===== */
@keyframes taskAdd {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.kanban-task.new {
    animation: taskAdd 0.3s ease-out;
}
        </style>
    @endpush

    <div class="py-6">
        <div class="max-w-full mx-auto">
            {{-- Messages de succès/erreur --}}
            @if(session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                    <div class="bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-800 rounded-lg p-4">
                        <div class="flex">
                            <i class="fas fa-check-circle text-green-400 mr-3 mt-0.5"></i>
                            <p class="text-green-800 dark:text-green-200">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                    <div class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-lg p-4">
                        <div class="flex">
                            <i class="fas fa-exclamation-circle text-red-400 mr-3 mt-0.5"></i>
                            <p class="text-red-800 dark:text-red-200">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tableau Kanban --}}
            <div class="kanban-board" id="kanbanBoard">
                @foreach(['Backlog', 'To Do', 'In Progress', 'To Be Checked', 'Done'] as $column)
                    <div class="kanban-column" data-column="{{ $column }}">
                        <div class="kanban-column-header">
                            <div class="kanban-column-title">
                                <div class="w-3 h-3 rounded-full 
                                    @if($column === 'Backlog') bg-gray-500
                                    @elseif($column === 'To Do') bg-blue-500
                                    @elseif($column === 'In Progress') bg-yellow-500
                                    @elseif($column === 'To Be Checked') bg-purple-500
                                    @else bg-green-500
                                    @endif">
                                </div>
                                <span>{{ $column }}</span>
                                <span class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">
                                    {{ $project->tasks()->where('column', $column)->count() }}
                                </span>
                            </div>
                        </div>

                        <div class="kanban-tasks-container" id="column-{{ $loop->index }}">
                            @foreach($project->tasks()->where('column', $column)->orderBy('created_at', 'desc')->get() as $task)
                                <div class="kanban-task" 
                                     data-task-id="{{ $task->id }}" 
                                     draggable="true"
                                     onclick="openTaskModal({{ $task->id }}, 'edit')">
                                    
                                    <div class="kanban-task-title">
                                        {{ $task->title }}
                                    </div>
                                    
                                    @if($task->description)
                                        <div class="kanban-task-description">
                                            {{ Str::limit($task->description, 80) }}
                                        </div>
                                    @endif

                                    @if($task->category)
                                        <div class="mb-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ $task->category }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="kanban-task-meta">
                                        <div class="kanban-task-assignee">
                                            <i class="fas fa-user"></i>
                                            <span>{{ $task->user->name ?? 'Non assigné' }}</span>
                                        </div>
                                        
                                        @if($task->due_date)
                                            <div class="kanban-task-due-date {{ $task->due_date < now() ? 'overdue' : '' }}">
                                                <i class="fas fa-calendar"></i>
                                                <span>{{ $task->due_date->format('d/m') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            {{-- Bouton ajouter tâche --}}
                            <button class="add-task-btn" onclick="openTaskModal(null, 'create', '{{ $column }}')">
                                <i class="fas fa-plus"></i>
                                Ajouter une tâche
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Modal de tâche --}}
    <div id="taskModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    <span id="modalTitle">Nouvelle tâche</span>
                </h3>
            </div>
            
            <form id="taskForm" class="px-6 py-4 space-y-4">
                <input type="hidden" id="taskMode" value="create">
                <input type="hidden" id="editTargetId" value="">
                <input type="hidden" id="taskColumn" value="">
                
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
                           placeholder="Marketing, Développement, etc."
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100">
                </div>

                <div>
                    <label for="taskDueDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Date d'échéance
                    </label>
                    <input type="date" 
                           id="taskDueDate"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100">
                </div>

                <div>
                    <label for="taskPriority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Priorité
                    </label>
                    <select id="taskPriority" 
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">Sélectionner une priorité</option>
                        <option value="low">Basse</option>
                        <option value="medium">Moyenne</option>
                        <option value="high">Élevée</option>
                    </select>
                </div>
            </form>
            
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex gap-3 justify-end">
                <button type="button" 
                        onclick="closeTaskModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Annuler
                </button>
                <button type="button" 
                        id="saveTaskBtn"
                        onclick="saveTask()"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                    Sauvegarder
                </button>
                <button type="button" 
                        id="deleteTaskBtn"
                        onclick="deleteTask()"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors hidden">
                    Supprimer
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
  @vite('resources/js/task.js')
@endpush
</x-app-layout>
