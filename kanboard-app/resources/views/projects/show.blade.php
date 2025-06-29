<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ $project->name }}
            </h2>
            
            {{-- Navigation responsive pour les vues --}}
            <nav class="flex flex-col sm:flex-row w-full sm:w-auto gap-2 sm:gap-4">
                <a href="{{ route('projects.show', $project) }}" 
                   class="px-3 py-2 rounded-md text-sm font-medium text-center bg-blue-600 text-white">
                    <i class="fas fa-columns mr-2"></i>Tableau Kanban
                </a>
                <a href="{{ route('project.members.index', $project) }}" 
                   class="px-3 py-2 rounded-md text-sm font-medium text-center text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                    <i class="fas fa-users mr-2"></i>Membres
                </a>
            </nav>
        </div>
    </x-slot>

    {{-- CHARGER LES CSS --}}
    @vite(['resources/css/show.css'])
    @vite(['resources/css/task.css'])

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Board Kanban --}}
            <div class="grid-kanban">
                @foreach(['Backlog', 'To Do', 'In Progress', 'To Be Checked', 'Done'] as $column)
                    <div class="kanban-column-container">
                        <div class="kanban-header">
                            <h3>{{ $column }}</h3>
                            <div class="kanban-menu">⋯</div>
                        </div>

                        <ul class="kanban-column" id="column-{{ Str::slug($column) }}" data-column="{{ $column }}">
                            {{-- Les tâches existantes --}}
                            @foreach($tasks->where('column', $column) as $task)
                                <li class="task-card" data-task-id="{{ $task->id }}">
                                    @if($task->category)
                                        <div class="task-badge" style="background-color: {{ $task->color ?? '#6b7280' }}">
                                            {{ $task->category }}
                                        </div>
                                    @endif
                                    
                                    <div class="task-title">{{ $task->title }}</div>
                                    
                                    <div class="task-user">
                                        {{ strtoupper(substr($task->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <button class="add-task-btn" data-column="{{ $column }}">
                            + Ajouter une carte
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Modal de création/édition de tâche --}}
    <div id="taskModal" class="modal hidden">
        <div class="modal-content">
            <h2 id="taskModalTitle">Nouvelle tâche</h2>

            <form id="taskForm">
                <div class="form-group">
                    <label for="taskTitle">Titre de la tâche *</label>
                    <input type="text" id="taskTitle" name="taskTitle" placeholder="Entrez le titre de la tâche" required>
                </div>

                <div class="form-group">
                    <label for="taskCategory">Catégorie / rôle</label>
                    <input type="text" id="taskCategory" name="taskCategory" placeholder="Ex: DevOps, Frontend...">
                </div>

                <div class="form-group">
                    <label for="taskColor">Couleur du badge</label>
                    <input type="color" id="taskColor" name="taskColor" value="#3b82f6">
                </div>

                {{-- INPUTS CACHÉS --}}
                <input type="hidden" id="taskColumn" name="taskColumn" value="">
                <input type="hidden" id="taskMode" name="taskMode" value="create">
                <input type="hidden" id="editTargetId" name="editTargetId" value="">

                <div class="modal-buttons">
                    <button type="button" id="cancelTaskModal" class="cancel">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </button>
                    <button type="button" id="submitTaskBtn" class="submit">
                        <i class="fas fa-save mr-2"></i>Créer la tâche
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- JAVASCRIPT PROPRE --}}
    @push('scripts')
    <script>
        // Variables globales
        const projectId = {{ $project->id }};
        const tasks = @json($tasks);
        const userInitials = @json($userInitials ?? strtoupper(substr(auth()->user()->name, 0, 1)));

        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Initialisation du projet Kanban:', projectId);
            
            const taskModal = document.getElementById('taskModal');
            const taskForm = document.getElementById('taskForm');
            
            if (!taskModal || !taskForm) {
                console.error('❌ Modal ou formulaire manquant');
                return;
            }
            
            // ===== FORCER LE MODAL À ÊTRE CACHÉ AU DÉMARRAGE =====
            taskModal.classList.add('hidden');
            console.log('✅ Modal forcé en état caché');
            
            // ===== ÉLÉMENTS DU FORMULAIRE =====
            const taskColumn = document.getElementById('taskColumn');
            const taskTitle = document.getElementById('taskTitle');
            const taskCategory = document.getElementById('taskCategory');
            const taskColor = document.getElementById('taskColor');
            const taskModalTitle = document.getElementById('taskModalTitle');
            
            // ===== BOUTONS D'AJOUT =====
            const addTaskBtns = document.querySelectorAll('.add-task-btn');
            console.log(`📝 ${addTaskBtns.length} boutons d'ajout trouvés`);
            
            addTaskBtns.forEach((btn, index) => {
                const column = btn.dataset.column;
                console.log(`   Bouton ${index + 1}: "${column}"`);
                
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    console.log(`🎯 Clic sur bouton: ${column}`);
                    
                    // Réinitialiser le formulaire
                    taskTitle.value = '';
                    taskCategory.value = '';
                    taskColor.value = '#3b82f6';
                    taskColumn.value = column;
                    
                    // Mettre à jour le titre
                    if (taskModalTitle) {
                        taskModalTitle.textContent = `Nouvelle tâche - ${column}`;
                    }
                    
                    // Ouvrir le modal
                    console.log('🔓 Ouverture du modal');
                    taskModal.classList.remove('hidden');
                    
                    // Focus sur le premier champ
                    setTimeout(() => {
                        if (taskTitle) {
                            taskTitle.focus();
                        }
                    }, 100);
                });
            });
            
            // ===== BOUTONS DE FERMETURE =====
            const cancelBtn = document.getElementById('cancelTaskModal');
            const submitBtn = document.getElementById('submitTaskBtn');
            
            function closeModal() {
                console.log('🔒 Fermeture du modal');
                taskModal.classList.add('hidden');
                
                // Réinitialiser le formulaire
                taskForm.reset();
                taskColumn.value = '';
            }
            
            // Bouton Annuler
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    closeModal();
                });
            }
            
            // Clic en dehors du modal
            taskModal.addEventListener('click', function(e) {
                if (e.target === taskModal) {
                    closeModal();
                }
            });
            
            // ===== SOUMISSION DU FORMULAIRE =====
            if (submitBtn) {
                submitBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    console.log('💾 Tentative de soumission');
                    
                    const title = taskTitle.value.trim();
                    const category = taskCategory.value.trim();
                    const color = taskColor.value;
                    const column = taskColumn.value;
                    
                    console.log('📋 Données du formulaire:', {
                        title,
                        category,
                        color,
                        column,
                        projectId
                    });
                    
                    // Validation
                    if (!title) {
                        alert('⚠️ Le titre est obligatoire');
                        taskTitle.focus();
                        return;
                    }
                    
                    if (!column) {
                        alert('❌ Erreur: colonne non définie');
                        return;
                    }
                    
                    // Simulation d'envoi (remplacer par vraie requête plus tard)
                    console.log('✅ Validation réussie, données prêtes pour envoi');
                    
                    // Créer la tâche visuellement (temporaire)
                    createTaskVisually(title, category, color, column);
                    
                    // Fermer le modal
                    closeModal();
                    
                    // Message de succès
                    showNotification(`Tâche "${title}" créée dans ${column}`, 'success');
                });
            }
            
            // ===== FERMETURE PAR ÉCHAP =====
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !taskModal.classList.contains('hidden')) {
                    closeModal();
                }
            });
            
            console.log('✅ Initialisation terminée');
        });
        
        // ===== FONCTIONS UTILITAIRES =====
        
        // Créer une tâche visuellement (temporaire, en attendant l'API)
        function createTaskVisually(title, category, color, column) {
            const columnEl = document.querySelector(`[data-column="${column}"]`);
            if (!columnEl) return;
            
            const taskId = 'temp-' + Date.now();
            const taskHtml = `
                <li class="task-card fade-in" data-task-id="${taskId}">
                    ${category ? `<div class="task-badge" style="background-color: ${color}">${category}</div>` : ''}
                    <div class="task-title">${title}</div>
                    <div class="task-user">${userInitials}</div>
                </li>
            `;
            
            columnEl.insertAdjacentHTML('beforeend', taskHtml);
            console.log(`✨ Tâche ajoutée visuellement dans ${column}`);
        }
        
        // Afficher une notification
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.textContent = message;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 12px 16px;
                border-radius: 8px;
                color: white;
                font-weight: 500;
                z-index: 9999;
                animation: slideIn 0.3s ease-out;
                background: ${type === 'success' ? '#10b981' : '#3b82f6'};
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
    
    <style>
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
    @endpush
</x-app-layout>
