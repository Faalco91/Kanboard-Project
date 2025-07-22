// Vérifier si nous sommes sur la page du projet Kanban
function isKanbanPage() {
    // Vérifications pour s'assurer qu'on est sur la bonne page
    const hasTaskModal = document.getElementById('taskModal') !== null;
    const isListView = window.location.pathname.includes('/list');
    const isCalendarView = window.location.pathname.includes('/calendar');
    const isStatsView = window.location.pathname.includes('/stats');
    const hasKanbanElements = document.querySelector('.kanban-column, .kanban-container, .columns-container') !== null;

    // Page Kanban : modal de tâche présent, pas de vue liste/calendrier/stats, éléments Kanban présents
    const result = hasTaskModal && !isListView && !isCalendarView && !isStatsView && hasKanbanElements;

    console.log('Vérification page Kanban:', {
        hasTaskModal,
        isListView,
        isCalendarView,
        isStatsView,
        hasKanbanElements,
        result
    });

    return result;
}

// Variable pour éviter les double-initialisations
let taskSystemInitialized = false;

// Charger le script uniquement sur les pages Kanban
if (isKanbanPage()) {
    document.addEventListener('DOMContentLoaded', () => {
        // Vérification supplémentaire après chargement DOM
        if (!isKanbanPage()) {
            console.log('Page non-Kanban détectée après DOMContentLoaded');
            return;
        }

        if (taskSystemInitialized) {
            console.log('Système déjà initialisé, arrêt');
            return;
        }

        console.log('Chargement de task.js sur page Kanban');
        taskSystemInitialized = true;

        // Cache des éléments DOM
        const DOM = {
            taskModal: document.getElementById('taskModal'),
            taskForm: document.getElementById('taskForm'),
            cancelBtn: document.getElementById('cancelTaskModal'),
            taskTitle: document.getElementById('taskTitle'),
            taskCategory: document.getElementById('taskCategory'),
            taskColor: document.getElementById('taskColor'),
            taskDate: document.getElementById('taskDueDate'),
            taskColumn: document.getElementById('taskColumn'),
            taskMode: document.getElementById('taskMode'),
            editTargetId: document.getElementById('editTargetId'),
            modalTitle: document.getElementById('modalTitle'),
            submitButtonText: document.getElementById('submitButtonText'),
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            projectId: document.querySelector('meta[name="project-id"]')?.getAttribute('content')
        };

        // Vérifications
        if (!DOM.taskModal || !DOM.taskForm) {
            console.log('Éléments du modal non trouvés - probablement sur une autre page');
            return;
        }

        console.log('Tous les éléments du modal trouvés');

        // Initialiser le drag & drop
        initializeFastDragDrop();

        // Charger les tâches existantes
        loadTasksFromServer();

        // Gestion des boutons d'ajout de tâches
        document.querySelectorAll('.add-task-btn').forEach((btn, index) => {
            // Éviter l'ajout de gestionnaires multiples
            if (btn.dataset.handlerAttached) return;
            btn.dataset.handlerAttached = 'true';

            // Mapping des colonnes par index
            const columns = ['Backlog', 'To Do', 'In Progress', 'To Be Checked', 'Done'];
            const columnName = columns[index] || 'To Do';

            btn.onclick = function (e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Création tâche pour:', columnName);
                window.openTaskModal(null, 'create', columnName);
            };
        });

        // Fermeture du modal
        if (DOM.cancelBtn) {
            DOM.cancelBtn.removeEventListener('click', closeModal);
            DOM.cancelBtn.addEventListener('click', closeModal);
        }

        // Fermer en cliquant en dehors du modal
        DOM.taskModal.removeEventListener('click', handleModalClick);
        DOM.taskModal.addEventListener('click', handleModalClick);

        // Fermer avec la touche Escape
        document.removeEventListener('keydown', handleEscapeKey);
        document.addEventListener('keydown', handleEscapeKey);

        // Gestion de la soumission du formulaire
        DOM.taskForm.removeEventListener('submit', handleFormSubmit);
        DOM.taskForm.addEventListener('submit', handleFormSubmit);
    });
} else {
    console.log('task.js ignoré - pas sur une page Kanban');
}

// Gestionnaires d'événements
function handleModalClick(e) {
    if (e.target === e.currentTarget) closeModal();
}

function handleEscapeKey(e) {
    if (e.key === 'Escape' && !document.getElementById('taskModal').classList.contains('hidden')) {
        closeModal();
    }
}

// Fonctions pour la gestion des tâches
const DOM_CACHE = {
    taskModal: null,
    csrfToken: null,
    projectId: null
};

function initializeDOM() {
    if (!DOM_CACHE.taskModal) {
        DOM_CACHE.taskModal = document.getElementById('taskModal');
        DOM_CACHE.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Récupérer le project ID
        DOM_CACHE.projectId = document.querySelector('meta[name="project-id"]')?.getAttribute('content');

        // Fallback: chercher dans l'URL
        if (!DOM_CACHE.projectId) {
            const urlMatch = window.location.pathname.match(/\/projects\/(\d+)/);
            if (urlMatch) {
                DOM_CACHE.projectId = urlMatch[1];
                console.log('Project ID trouvé dans l\'URL:', DOM_CACHE.projectId);
            }
        }

        // Fallback: chercher dans un autre endroit du DOM
        if (!DOM_CACHE.projectId) {
            const projectElement = document.querySelector('[data-project-id]');
            if (projectElement) {
                DOM_CACHE.projectId = projectElement.dataset.projectId;
                console.log('Project ID trouvé dans data-project-id:', DOM_CACHE.projectId);
            }
        }

        if (!DOM_CACHE.projectId) {
            console.error('Project ID non trouvé');
        }
    }
}

function closeModal() {
    initializeDOM();
    if (DOM_CACHE.taskModal) {
        DOM_CACHE.taskModal.classList.add('hidden');
    }
}

// Gestion de la soumission du formulaire
function handleFormSubmit(e) {
    e.preventDefault();
    e.stopPropagation();

    const title = document.getElementById('taskTitle').value.trim();
    const description = document.getElementById('taskDescription')?.value?.trim() || '';
    const category = document.getElementById('taskCategory')?.value?.trim() || '';

    // Support des deux noms d'éléments
    const dateElement = document.getElementById('taskDueDate') || document.getElementById('taskDate');
    const date = dateElement?.value || '';

    const priority = document.getElementById('taskPriority')?.value || 'medium';
    const column = document.getElementById('taskColumn').value;
    const mode = document.getElementById('taskMode').value;

    console.log('Données:', { title, column, mode, priority, date });

    if (!title) {
        alert('Titre requis');
        return;
    }

    const btn = document.getElementById('saveTaskBtn');
    if (!btn) {
        console.error('Bouton de sauvegarde non trouvé');
        return;
    }

    // Éviter les double-clics
    if (btn.disabled) {
        console.log('Bouton déjà désactivé, requête en cours');
        return;
    }

    btn.disabled = true;
    btn.textContent = 'Sauvegarde...';

    if (mode === 'create') {
        // Utiliser la fonction de création complète
        createTaskFastComplete(title, description, category, priority, column, date)
            .finally(() => {
                btn.disabled = false;
                btn.textContent = 'Sauvegarder';
            });
    } else {
        const taskId = document.getElementById('editTargetId').value;
        // Passer tous les paramètres pour la mise à jour
        updateTaskFastComplete(taskId, title, description, category, priority, date)
            .finally(() => {
                btn.disabled = false;
                btn.textContent = 'Sauvegarder';
            });
    }
}

// Création de tâche
function createTaskFastComplete(title, description, category, priority, column, date = '') {
    console.log('Début création tâche:', { title, description, category, priority, column, date });

    initializeDOM();

    // Validation des données avant envoi
    if (!DOM_CACHE.projectId) {
        console.error('DOM_CACHE.projectId non défini');
        showFastNotification('Erreur: ID du projet non trouvé', 'error');
        return Promise.reject('Project ID manquant');
    }

    const projectIdNumber = parseInt(DOM_CACHE.projectId);
    if (isNaN(projectIdNumber)) {
        console.error('DOM_CACHE.projectId n\'est pas un nombre:', DOM_CACHE.projectId);
        showFastNotification('Erreur: ID du projet invalide', 'error');
        return Promise.reject('Project ID invalide');
    }

    if (!column || column === 'create') {
        console.error('Colonne invalide:', column);
        showFastNotification('Erreur: Colonne non définie', 'error');
        return Promise.reject('Colonne invalide');
    }

    const taskData = {
        title: title,
        description: description || '',
        category: category || '',
        column: column,
        project_id: projectIdNumber,
        priority: priority || 'medium'
    };

    if (date) {
        taskData.due_date = date;
    }

    console.log('Données de création:', taskData);

    return fetch('/tasks', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': DOM_CACHE.csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify(taskData)
    })
        .then(response => {
            console.log('Statut réponse création:', response.status);
            if (!response.ok) {
                return response.json().then(err => {
                    console.error('Erreur serveur détaillée:', err);
                    throw err;
                });
            }
            return response.json();
        })
        .then(response => {
            console.log('Tâche créée avec succès:', response);
            showFastNotification('Tâche créée avec succès', 'success');
            closeModal();

            // Ajouter au DOM ou recharger la page
            if (response && response.id) {
                addTaskToDOMComplete(response, column);
            } else {
                console.log('Rechargement de la page dans 500ms');
                setTimeout(() => window.location.reload(), 500);
            }

            return response;
        })
        .catch(error => {
            console.error('Erreur création complète:', error);

            let errorMessage = 'Erreur lors de la création';
            if (error.errors) {
                const errorMessages = [];
                Object.keys(error.errors).forEach(field => {
                    errorMessages.push(`${field}: ${error.errors[field].join(', ')}`);
                });
                errorMessage = errorMessages.join(' | ');
            } else if (error.error) {
                errorMessage = error.error;
            } else if (error.message) {
                errorMessage = error.message;
            }

            showFastNotification(errorMessage, 'error');
            throw error;
        });
}

// ===== FONCTIONS GLOBALES =====
window.openTaskModal = function (taskId = null, mode = 'create', column = 'To Do') {
    console.log('Modal simple:', { taskId, mode, column });

    const modal = document.getElementById('taskModal');
    const form = document.getElementById('taskForm');

    if (!modal || !form) {
        console.error('Modal ou formulaire non trouvé');
        return;
    }

    // Reset complet du formulaire
    form.reset();

    // Configuration simple
    document.getElementById('taskMode').value = mode;
    document.getElementById('taskColumn').value = column;
    document.getElementById('modalTitle').textContent = mode === 'create' ? 'Nouvelle tâche' : 'Modifier la tâche';

    if (mode === 'edit' && taskId) {
        document.getElementById('editTargetId').value = taskId;
        const deleteBtn = document.getElementById('deleteTaskBtn');
        if (deleteBtn) deleteBtn.classList.remove('hidden');
        loadTaskDataFast(taskId);
    } else {
        document.getElementById('editTargetId').value = '';
        const deleteBtn = document.getElementById('deleteTaskBtn');
        if (deleteBtn) deleteBtn.classList.add('hidden');

        // Valeurs par défaut pour création
        const priorityInput = document.getElementById('taskPriority');
        if (priorityInput) priorityInput.value = 'medium';
    }

    modal.classList.remove('hidden');

    // Focus sur le titre après un court délai pour assurer que le modal est visible
    setTimeout(() => {
        const titleInput = document.getElementById('taskTitle');
        if (titleInput) titleInput.focus();
    }, 100);
};

// Protection contre les multiples chargements
if (window.taskSystemLoaded) {
    console.warn('task.js déjà chargé, éviter le double chargement');
} else {
    window.taskSystemLoaded = true;
    console.log('Système de tâches chargé et protégé contre les doublons');
}

// Mise à jour de tâche avec tous les champs
function updateTaskFastComplete(taskId, title, description, category, priority, date = '') {
    console.log('Mise à jour tâche complète:', {
        taskId,
        title,
        description,
        category,
        priority,
        date
    });

    initializeDOM();

    // Construire l'objet de données avec tous les champs
    const updateData = {
        title: title.trim(),
        description: description.trim(),
        category: category.trim(),
        priority: priority
    };

    // Ajouter la date seulement si elle est fournie
    if (date && date.trim()) {
        updateData.due_date = date.trim();
    }

    console.log('Données de mise à jour complètes:', updateData);

    return fetch(`/tasks/${taskId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': DOM_CACHE.csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify(updateData)
    })
        .then(response => {
            console.log('Statut réponse mise à jour:', response.status);

            if (!response.ok) {
                return response.json().then(err => {
                    console.error('Erreur serveur détaillée:', err);
                    throw err;
                });
            }
            return response.json();
        })
        .then(response => {
            console.log('Tâche mise à jour avec succès:', response);
            showFastNotification('Tâche mise à jour avec succès', 'success');
            closeModal();

            const completeTaskData = {
                // D'abord les données de la réponse serveur (si disponibles)
                ...response,
                id: taskId,
                title: updateData.title,
                description: updateData.description,
                category: updateData.category,
                priority: updateData.priority,
                due_date: updateData.due_date
            };

            console.log('Données complètes pour mise à jour DOM:', completeTaskData);

            // Mise à jour DOM complète avec les données correctes
            updateTaskInDOMComplete(taskId, completeTaskData);

            return response;
        })
        .catch(error => {
            console.error('Erreur mise à jour:', error);

            let errorMessage = 'Erreur lors de la mise à jour';
            if (error.errors) {
                // Gérer les erreurs de validation Laravel
                const errorMessages = [];
                Object.keys(error.errors).forEach(field => {
                    errorMessages.push(`${field}: ${error.errors[field].join(', ')}`);
                });
                errorMessage = errorMessages.join(' | ');
            } else if (error.message) {
                errorMessage = error.message;
            } else if (error.error) {
                errorMessage = error.error;
            }

            showFastNotification(errorMessage, 'error');
            throw error;
        });
}

// Drag & Drop
let draggedTask = null;
let dragStartTime = 0;

function initializeFastDragDrop() {
    console.log('Initialisation du drag & drop RAPIDE');

    // Cache des colonnes
    const columns = document.querySelectorAll('.kanban-tasks-container');
    console.log(`${columns.length} colonnes trouvées`);

    if (columns.length === 0) return;

    // Initialiser toutes les tâches existantes pour le drag & drop
    document.querySelectorAll('.kanban-task').forEach(task => {
        if (task.dataset.dragInitialized) return;
        task.dataset.dragInitialized = 'true';

        task.draggable = true;
        task.addEventListener('dragstart', handleDragStartFast, { passive: true });
        task.addEventListener('dragend', handleDragEndFast, { passive: true });
    });

    // Initialiser les colonnes pour recevoir les tâches
    columns.forEach(column => {
        if (column.dataset.dropInitialized) return;
        column.dataset.dropInitialized = 'true';

        column.addEventListener('dragover', handleDragOverFast, { passive: false });
        column.addEventListener('drop', handleDropFast, { passive: false });
        column.addEventListener('dragenter', handleDragEnterFast, { passive: true });
        column.addEventListener('dragleave', handleDragLeaveFast, { passive: true });
    });

    console.log('Drag & drop initialisé');
}

function handleDragStartFast(e) {
    draggedTask = this;
    dragStartTime = performance.now();
    this.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', this.dataset.taskId);
}

function handleDragEndFast(e) {
    this.classList.remove('dragging');
    document.querySelectorAll('.drag-over').forEach(el => el.classList.remove('drag-over'));
    console.log(`🏁 Drag terminé en ${Math.round(performance.now() - dragStartTime)}ms`);
}

function handleDragOverFast(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
}

function handleDragEnterFast(e) {
    this.closest('.kanban-column')?.classList.add('drag-over');
}

function handleDragLeaveFast(e) {
    const rect = this.getBoundingClientRect();
    if (e.clientX < rect.left || e.clientX > rect.right ||
        e.clientY < rect.top || e.clientY > rect.bottom) {
        this.closest('.kanban-column')?.classList.remove('drag-over');
    }
}

function handleDropFast(e) {
    e.preventDefault();
    e.stopPropagation();

    const column = this.closest('.kanban-column');
    const newColumn = column?.dataset.column;
    const taskId = draggedTask?.dataset.taskId;

    if (!taskId || !newColumn) return;

    column.classList.remove('drag-over');

    // Déplacement visuel immédiat
    if (draggedTask && this !== draggedTask.parentElement) {
        const oldParent = draggedTask.parentElement;
        this.appendChild(draggedTask);

        // Mise à jour immédiate des compteurs
        updateCounterFast(this);
        updateCounterFast(oldParent);
    }

    // Mise à jour serveur en arrière-plan
    updateTaskColumnFast(taskId, newColumn).catch(() => {
        showFastNotification('Erreur lors du déplacement', 'error');
    });
}

function updateTaskColumnFast(taskId, newColumn) {
    initializeDOM();

    return fetch(`/tasks/${taskId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': DOM_CACHE.csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ column: newColumn })
    })
        .then(response => response.ok ? response.json() : Promise.reject(response))
        .then(data => {
            showFastNotification(`Déplacé vers "${newColumn}"`, 'success');
            return data;
        })
        .catch(error => {
            console.error('❌ Erreur déplacement:', error);
            throw error;
        });
}

// Fonctions utilitaires

function loadTasksFromServer() {
    // Pas de chargement serveur, juste activer le drag sur les tâches existantes
    document.querySelectorAll('.kanban-task:not([data-drag-initialized])').forEach(task => {
        task.dataset.dragInitialized = 'true';
        task.draggable = true;
        task.addEventListener('dragstart', handleDragStartFast, { passive: true });
        task.addEventListener('dragend', handleDragEndFast, { passive: true });
    });
}

function showFastNotification(message, type = 'info') {
    // Notification rapide
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-3 rounded-lg text-white z-50 transition-all duration-200 ${type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
    notification.textContent = message;
    document.body.appendChild(notification);

    // Animation d'entrée
    requestAnimationFrame(() => {
        notification.style.transform = 'translateX(0)';
        notification.style.opacity = '1';
    });

    // Suppression rapide
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 200);
    }, 2000);
}

function addTaskToDOMComplete(task, column) {
    console.log('Ajout tâche complète au DOM:', task);

    // Créer l'élément de tâche avec tous les détails
    const taskElement = document.createElement('div');
    taskElement.className = 'kanban-task';
    taskElement.draggable = true;
    taskElement.dataset.taskId = task.id;
    taskElement.dataset.dragInitialized = 'true';
    taskElement.setAttribute('onclick', `openTaskModal(${task.id}, 'edit')`);

    let innerHTML = `<div class="kanban-task-title">${task.title}</div>`;

    // Ajouter la description si elle existe
    if (task.description) {
        const shortDesc = task.description.length > 80 ?
            task.description.substring(0, 80) + '...' : task.description;
        innerHTML += `<div class="kanban-task-description">${shortDesc}</div>`;
    }

    // Ajouter la catégorie si elle existe
    if (task.category) {
        innerHTML += `
            <div class="mb-2">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    ${task.category}
                </span>
            </div>
        `;
    }

    // Ajouter les métadonnées
    innerHTML += `
        <div class="kanban-task-meta">
            <div class="kanban-task-assignee">
                <i class="fas fa-user"></i>
                <span>${task.user?.name || 'Non assigné'}</span>
            </div>
    `;

    // Ajouter la date d'échéance si elle existe
    if (task.due_date) {
        const date = new Date(task.due_date);
        const isOverdue = date < new Date();
        innerHTML += `
            <div class="kanban-task-due-date ${isOverdue ? 'overdue' : ''}">
                <i class="fas fa-calendar"></i>
                <span>${date.getDate()}/${date.getMonth() + 1}</span>
            </div>
        `;
    }

    innerHTML += '</div>'; // Fermer kanban-task-meta

    taskElement.innerHTML = innerHTML;

    // Ajouter les événements de drag
    taskElement.addEventListener('dragstart', handleDragStartFast, { passive: true });
    taskElement.addEventListener('dragend', handleDragEndFast, { passive: true });

    // Trouver la colonne et insérer avant le bouton "Ajouter une tâche"
    const columnElement = document.querySelector(`[data-column="${column}"] .kanban-tasks-container`);
    if (columnElement) {
        const addButton = columnElement.querySelector('.add-task-btn');
        if (addButton) {
            columnElement.insertBefore(taskElement, addButton);
        } else {
            columnElement.appendChild(taskElement);
        }

        updateCounterFast(columnElement);

        // Animation d'entrée
        taskElement.classList.add('task-enter');
        setTimeout(() => taskElement.classList.remove('task-enter'), 300);

        console.log('Tâche ajoutée au DOM avec succès');
    } else {
        console.warn('Colonne non trouvée, rechargement de la page');
        setTimeout(() => window.location.reload(), 500);
    }
}

// Mise à jour DOM complète avec priorité et description
function updateTaskInDOMComplete(taskId, task) {
    console.log('Mise à jour DOM complète pour la tâche:', taskId, task);

    const taskElement = document.querySelector(`[data-task-id="${taskId}"]`);
    if (!taskElement) {
        console.warn('Élément DOM de la tâche non trouvé');
        setTimeout(() => window.location.reload(), 1000);
        return;
    }

    // Mise à jour du titre
    const titleEl = taskElement.querySelector('.kanban-task-title');
    if (titleEl && task.title) {
        titleEl.textContent = task.title;
        console.log('Titre mis à jour:', task.title);
    }

    // Mise à jour de la description
    let descEl = taskElement.querySelector('.kanban-task-description');
    if (task.description && task.description.trim()) {
        console.log('Mise à jour description:', task.description);

        if (!descEl) {
            // Créer l'élément description s'il n'existe pas
            descEl = document.createElement('div');
            descEl.className = 'kanban-task-description text-gray-600 text-sm mt-2';
            if (titleEl && titleEl.parentNode) {
                titleEl.parentNode.insertBefore(descEl, titleEl.nextSibling);
            } else {
                taskElement.appendChild(descEl);
            }
            console.log('Élément description créé');
        }

        // Tronquer la description si elle est trop longue
        const displayDesc = task.description.length > 80 ?
            task.description.substring(0, 80) + '...' : task.description;
        descEl.textContent = displayDesc;
        console.log('Description mise à jour:', displayDesc);
    } else if (descEl) {
        // Supprimer la description si elle est vide
        descEl.remove();
        console.log('Description supprimée (vide ou null)');
    } else {
        console.log('Pas de description à mettre à jour');
    }

    // Mise à jour de la catégorie
    let categoryEl = taskElement.querySelector('.inline-flex');
    if (task.category && task.category.trim()) {
        if (!categoryEl) {
            // Créer l'élément catégorie s'il n'existe pas
            const categoryDiv = document.createElement('div');
            categoryDiv.className = 'mb-2';
            categoryEl = document.createElement('span');
            categoryEl.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
            categoryDiv.appendChild(categoryEl);
            taskElement.appendChild(categoryDiv);
        }
        categoryEl.textContent = task.category;
        console.log('Catégorie mise à jour:', task.category);
    } else if (categoryEl) {
        // Supprimer la catégorie si elle est vide
        const categoryContainer = categoryEl.closest('.mb-2');
        if (categoryContainer) {
            categoryContainer.remove();
        } else {
            categoryEl.remove();
        }
        console.log('Catégorie supprimée (vide)');
    }

    // Mise à jour de la date d'échéance
    let dueDateEl = taskElement.querySelector('.kanban-task-due-date');
    if (task.due_date && task.due_date.trim()) {
        if (!dueDateEl) {
            // Créer l'élément date s'il n'existe pas
            let metaEl = taskElement.querySelector('.kanban-task-meta');
            if (!metaEl) {
                metaEl = document.createElement('div');
                metaEl.className = 'kanban-task-meta';
                taskElement.appendChild(metaEl);
            }

            dueDateEl = document.createElement('div');
            dueDateEl.className = 'kanban-task-due-date flex items-center gap-1';
            dueDateEl.innerHTML = '<i class="fas fa-calendar"></i><span></span>';
            metaEl.appendChild(dueDateEl);
        }

        const dateSpan = dueDateEl.querySelector('span');
        if (dateSpan) {
            const date = new Date(task.due_date);
            const formattedDate = `${date.getDate()}/${date.getMonth() + 1}`;
            dateSpan.textContent = formattedDate;

            // Vérifier si la date est dépassée
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            date.setHours(0, 0, 0, 0);

            if (date < today) {
                dueDateEl.classList.add('overdue');
            } else {
                dueDateEl.classList.remove('overdue');
            }

            console.log('Date d\'échéance mise à jour:', formattedDate);
        }
    } else if (dueDateEl) {
        // Supprimer la date si elle est vide
        dueDateEl.remove();
        console.log('Date d\'échéance supprimée (vide)');
    }

    // Mise à jour visuelle de la priorité
    if (task.priority) {
        console.log('Mise à jour priorité:', task.priority);

        // Supprimer les anciennes classes de priorité
        taskElement.classList.remove('priority-low', 'priority-medium', 'priority-high', 'priority-urgent');

        // Ajouter la nouvelle classe de priorité
        taskElement.classList.add(`priority-${task.priority}`);

        // Gestion de l'indicateur visuel de priorité
        let priorityEl = taskElement.querySelector('.kanban-task-priority');

        if (task.priority !== 'medium') {
            // Afficher l'indicateur pour toutes les priorités sauf medium
            if (!priorityEl) {
                priorityEl = document.createElement('div');
                priorityEl.className = 'kanban-task-priority text-xs font-semibold mt-1';
                taskElement.appendChild(priorityEl);
            }

            const priorityConfig = {
                'low': { color: 'text-green-600', label: '🟢 Faible' },
                'high': { color: 'text-orange-600', label: '🟠 Haute' },
                'urgent': { color: 'text-red-600', label: '🔴 Urgente' }
            };

            const config = priorityConfig[task.priority];
            if (config) {
                priorityEl.className = `kanban-task-priority text-xs font-semibold mt-1 ${config.color}`;
                priorityEl.textContent = config.label;
                console.log('Indicateur de priorité ajouté:', config.label);
            }
        } else if (priorityEl) {
            // Supprimer l'indicateur pour la priorité medium
            priorityEl.remove();
            console.log('Indicateur de priorité supprimé (medium)');
        }

        console.log('Priorité mise à jour:', task.priority);
    } else {
        console.log('Pas de priorité dans les données:', task);
    }

    // Animation de mise à jour
    taskElement.classList.add('task-updated');
    setTimeout(() => {
        taskElement.classList.remove('task-updated');
    }, 300);

    // Ajouter du CSS pour l'animation si pas déjà présent
    if (!document.querySelector('#task-update-styles')) {
        const style = document.createElement('style');
        style.id = 'task-update-styles';
        style.textContent = `
            .task-updated {
                transform: scale(1.02);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                transition: all 0.3s ease;
            }
            .task-enter {
                opacity: 0;
                transform: translateY(-10px);
                animation: taskEnter 0.3s ease forwards;
            }
            @keyframes taskEnter {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            .priority-urgent { border-left: 4px solid #dc2626; }
            .priority-high { border-left: 4px solid #ea580c; }
            .priority-medium { border-left: 4px solid #ca8a04; }
            .priority-low { border-left: 4px solid #16a34a; }
        `;
        document.head.appendChild(style);
    }

    console.log('DOM mis à jour complètement avec tous les champs');
}

function updateCounterFast(columnElement) {
    if (!columnElement) return;

    const column = columnElement.closest('.kanban-column');
    if (!column) return;

    const taskCount = columnElement.querySelectorAll('.kanban-task').length;
    const counter = column.querySelector('.text-xs.bg-gray-100, .text-xs.bg-gray-700');
    if (counter) {
        counter.textContent = taskCount;
    }

    console.log(`Compteur mis à jour: ${taskCount} tâches`);
}

function loadTaskDataFast(taskId) {
    console.log('Chargement des données de la tâche:', taskId);

    const card = document.querySelector(`[data-task-id="${taskId}"]`);
    if (card) {
        const titleEl = card.querySelector('.kanban-task-title');
        const descEl = card.querySelector('.kanban-task-description');
        const categoryEl = card.querySelector('.inline-flex');
        const dueDateEl = card.querySelector('.kanban-task-due-date span');

        if (titleEl) {
            const titleInput = document.getElementById('taskTitle');
            if (titleInput) titleInput.value = titleEl.textContent.trim();
        }

        if (descEl) {
            const descInput = document.getElementById('taskDescription');
            if (descInput) descInput.value = descEl.textContent.trim();
        }

        if (categoryEl) {
            const categoryInput = document.getElementById('taskCategory');
            if (categoryInput) categoryInput.value = categoryEl.textContent.trim();
        }

        if (dueDateEl) {
            const dateText = dueDateEl.textContent.trim();
            if (dateText.includes('/')) {
                const [day, month] = dateText.split('/');
                const currentYear = new Date().getFullYear();
                const date = new Date(currentYear, parseInt(month) - 1, parseInt(day));
                const dateInput = document.getElementById('taskDueDate');
                if (dateInput) dateInput.value = date.toISOString().split('T')[0];
            }
        }

        const priorityInput = document.getElementById('taskPriority');
        if (priorityInput && priorityInput.value === '') {
            priorityInput.value = 'medium';
        }

        console.log('Données chargées depuis la carte DOM');
    }

    loadTaskDataFromAPI(taskId);
}

function loadTaskDataFromAPI(taskId) {
    console.log('Chargement des données complètes depuis l\'API:', taskId);

    initializeDOM();

    fetch(`/tasks/${taskId}/data`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': DOM_CACHE.csrfToken
        }
    })
        .then(response => {
            if (!response.ok) {
                return fetch(`/tasks/${taskId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': DOM_CACHE.csrfToken
                    }
                });
            }
            return response;
        })
        .then(response => {
            if (!response.ok) {
                console.warn('Impossible de charger les données depuis l\'API');
                return null;
            }
            return response.json();
        })
        .then(task => {
            if (!task) {
                const priorityInput = document.getElementById('taskPriority');
                if (priorityInput && !priorityInput.value) {
                    priorityInput.value = 'medium';
                }
                return;
            }

            console.log('Données complètes reçues:', task);

            const elements = {
                title: document.getElementById('taskTitle'),
                description: document.getElementById('taskDescription'),
                category: document.getElementById('taskCategory'),
                dueDate: document.getElementById('taskDueDate'),
                priority: document.getElementById('taskPriority')
            };

            if (elements.title && task.title) {
                elements.title.value = task.title;
            }

            if (elements.description && task.description) {
                elements.description.value = task.description;
            }

            if (elements.category && task.category) {
                elements.category.value = task.category;
            }

            if (elements.dueDate && task.due_date) {
                const date = new Date(task.due_date);
                elements.dueDate.value = date.toISOString().split('T')[0];
            }

            if (elements.priority) {
                if (task.priority) {
                    elements.priority.value = task.priority;
                    console.log('Priorité remplie depuis API:', task.priority);
                } else {
                    elements.priority.value = 'medium';
                    console.log('Priorité par défaut définie (API ne retourne pas de priorité)');
                }
            }

            console.log('Formulaire rempli avec les données API');
        })
        .catch(error => {
            console.warn('Erreur lors du chargement API:', error);
            console.log('Utilisation des données disponibles dans le DOM');

            const priorityInput = document.getElementById('taskPriority');
            if (priorityInput && !priorityInput.value) {
                priorityInput.value = 'medium';
                console.log('🔧 Priorité par défaut appliquée après erreur API');
            }
        });
}

// ===== FONCTIONS GLOBALES =====

window.openTaskModal = function (taskId = null, mode = 'create', column = 'To Do') {
    console.log('Modal simple:', { taskId, mode, column });

    const modal = document.getElementById('taskModal');
    const form = document.getElementById('taskForm');

    if (!modal || !form) {
        console.error('Modal ou formulaire non trouvé');
        return;
    }

    // Reset complet du formulaire
    form.reset();

    // Configuration simple
    document.getElementById('taskMode').value = mode;
    document.getElementById('taskColumn').value = column;
    document.getElementById('modalTitle').textContent = mode === 'create' ? 'Nouvelle tâche' : 'Modifier la tâche';

    if (mode === 'edit' && taskId) {
        document.getElementById('editTargetId').value = taskId;
        const deleteBtn = document.getElementById('deleteTaskBtn');
        if (deleteBtn) deleteBtn.classList.remove('hidden');
        loadTaskDataFast(taskId);
    } else {
        document.getElementById('editTargetId').value = '';
        const deleteBtn = document.getElementById('deleteTaskBtn');
        if (deleteBtn) deleteBtn.classList.add('hidden');

        // Valeurs par défaut pour création
        const priorityInput = document.getElementById('taskPriority');
        if (priorityInput) priorityInput.value = 'medium';
    }

    modal.classList.remove('hidden');

    // Focus sur le titre après un court délai pour assurer que le modal est visible
    setTimeout(() => {
        const titleInput = document.getElementById('taskTitle');
        if (titleInput) titleInput.focus();
    }, 100);
};

window.closeTaskModal = function () {
    const modal = document.getElementById('taskModal');
    if (modal) {
        modal.classList.add('hidden');
    }
};

window.saveTask = function () {
    const form = document.getElementById('taskForm');
    if (form) {
        form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    }
};

window.deleteTask = function () {
    const taskId = document.getElementById('editTargetId').value;
    if (!taskId) {
        console.error('Aucun ID de tâche pour suppression');
        return;
    }

    if (!confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')) {
        return;
    }

    console.log('Suppression de la tâche:', taskId);

    // Désactiver le bouton de suppression pendant la requête
    const deleteBtn = document.getElementById('deleteTaskBtn');
    if (deleteBtn) {
        deleteBtn.disabled = true;
        deleteBtn.textContent = 'Suppression...';
    }

    initializeDOM();

    fetch(`/tasks/${taskId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': DOM_CACHE.csrfToken,
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    console.error('Erreur suppression:', err);
                    throw err;
                });
            }
            return response.json();
        })
        .then(result => {
            console.log('Tâche supprimée avec succès:', result);
            showFastNotification('Tâche supprimée avec succès', 'success');

            // Fermer le modal
            closeModal();

            // Supprimer l'élément du DOM
            const taskElement = document.querySelector(`[data-task-id="${taskId}"]`);
            if (taskElement) {
                const parentColumn = taskElement.closest('.kanban-tasks-container');
                taskElement.remove();

                // Mettre à jour le compteur
                if (parentColumn) {
                    updateCounterFast(parentColumn);
                }

                console.log('Tâche supprimée du DOM');
            } else {
                // Si on ne trouve pas l'élément, recharger la page
                console.log('Rechargement de la page après suppression');
                setTimeout(() => window.location.reload(), 500);
            }
        })
        .catch(error => {
            console.error('Erreur lors de la suppression:', error);

            let errorMessage = 'Erreur lors de la suppression';
            if (error.message) {
                errorMessage = error.message;
            } else if (error.error) {
                errorMessage = error.error;
            }

            showFastNotification(errorMessage, 'error');
        })
        .finally(() => {
            // Réactiver le bouton
            if (deleteBtn) {
                deleteBtn.disabled = false;
                deleteBtn.textContent = 'Supprimer';
            }
        });
};

// Fonctions utilitaires

// Fonction pour éviter les duplications d'événements
function ensureEventNotDuplicated(element, eventType, handler) {
    if (!element) return;

    // Supprimer l'ancien gestionnaire s'il existe
    element.removeEventListener(eventType, handler);

    // Ajouter le nouveau gestionnaire
    element.addEventListener(eventType, handler);
}

// Fonction pour nettoyer les anciens gestionnaires
function cleanupEventHandlers() {
    // Nettoyer les boutons d'ajout de tâche
    document.querySelectorAll('.add-task-btn').forEach(btn => {
        btn.onclick = null;
        btn.removeAttribute('data-handler-attached');
    });

    // Nettoyer les tâches draggables
    document.querySelectorAll('.kanban-task').forEach(task => {
        task.removeAttribute('data-drag-initialized');
    });

    // Nettoyer les colonnes
    document.querySelectorAll('.kanban-tasks-container').forEach(column => {
        column.removeAttribute('data-drop-initialized');
    });

    console.log('Gestionnaires d\'événements nettoyés');
}

// Fonction pour réinitialiser le système
function reinitializeTaskSystem() {
    if (taskSystemInitialized) {
        console.log('Réinitialisation du système de tâches');
        cleanupEventHandlers();
        taskSystemInitialized = false;
    }
}

// Fonction de debug pour identifier les doublons
function debugTaskSystem() {
    console.log('Debug du système de tâches:');
    console.log('- Boutons add-task avec gestionnaires:',
        document.querySelectorAll('.add-task-btn[data-handler-attached="true"]').length);
    console.log('- Tâches avec drag initialisé:',
        document.querySelectorAll('.kanban-task[data-drag-initialized="true"]').length);
    console.log('- Colonnes avec drop initialisé:',
        document.querySelectorAll('.kanban-tasks-container[data-drop-initialized="true"]').length);
    console.log('- Système initialisé:', taskSystemInitialized);
}

// Fonction pour vérifier l'intégrité du DOM
function validateDOMIntegrity() {
    const issues = [];

    // Vérifier les doublons d'ID
    const taskIds = [];
    document.querySelectorAll('[data-task-id]').forEach(el => {
        const id = el.dataset.taskId;
        if (taskIds.includes(id)) {
            issues.push(`ID dupliqué trouvé: ${id}`);
        } else {
            taskIds.push(id);
        }
    });

    // Vérifier les éléments requis
    const requiredElements = ['taskModal', 'taskForm', 'taskTitle', 'taskColumn'];
    requiredElements.forEach(id => {
        if (!document.getElementById(id)) {
            issues.push(`Élément requis manquant: ${id}`);
        }
    });

    if (issues.length > 0) {
        console.warn('Problèmes d\'intégrité DOM détectés:', issues);
        return false;
    }

    console.log('Intégrité DOM validée');
    return true;
}

// Gestionnaire global d'erreurs pour le système de tâches
window.addEventListener('error', function (event) {
    if (event.filename && event.filename.includes('task.js')) {
        console.error('Erreur dans task.js:', event.error);
        showFastNotification('Erreur système détectée', 'error');
    }
});

// Exposer les fonctions de debug en mode développement
if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev')) {
    window.debugTaskSystem = debugTaskSystem;
    window.validateDOMIntegrity = validateDOMIntegrity;
    window.reinitializeTaskSystem = reinitializeTaskSystem;
    console.log('Fonctions de debug disponibles');
}
