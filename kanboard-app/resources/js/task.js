// Vérifier si nous sommes sur la page du projet Kanban
function isKanbanPage() {
    return document.getElementById('taskModal') !== null;
}

// Ne charger le script que si on est sur une page Kanban
if (isKanbanPage()) {
    document.addEventListener('DOMContentLoaded', () => {
        console.log('🔧 Chargement de task.js sur page Kanban');

        // Vérifier que tous les éléments existent
        const taskModal = document.getElementById('taskModal');
        const taskForm = document.getElementById('taskForm');
        const cancelBtn = document.getElementById('cancelTaskModal');
        const taskTitle = document.getElementById('taskTitle');
        const taskCategory = document.getElementById('taskCategory');
        const taskColor = document.getElementById('taskColor');
        const taskDate = document.getElementById('taskDate');
        const taskColumn = document.getElementById('taskColumn');
        const taskMode = document.getElementById('taskMode');
        const editTargetId = document.getElementById('editTargetId');

        // Vérifications
        if (!taskModal || !taskForm) {
            console.log('ℹ️ Éléments du modal non trouvés - probablement sur une autre page');
            return;
        }

        console.log('✅ Tous les éléments du modal trouvés');

        // Vérifier les éléments optionnels
        const modalTitle = document.getElementById('modalTitle');
        const submitButtonText = document.getElementById('submitButtonText');

        if (!modalTitle) {
            console.warn('⚠️ Element modalTitle manquant');
        }
        if (!submitButtonText) {
            console.warn('⚠️ Element submitButtonText manquant');
        }

        // Initialiser Sortable pour le drag & drop
        initializeDragDrop();

        // Charger les tâches existantes
        loadTasksFromServer();

        // ===== GESTION DES BOUTONS D'AJOUT =====
        document.querySelectorAll('.add-task-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();

                if (!taskMode || !taskTitle || !taskCategory || !taskColor || !taskColumn) {
                    console.warn('⚠️ Éléments du formulaire manquants');
                    return;
                }

                taskMode.value = 'create';
                taskTitle.value = '';
                taskCategory.value = '';
                taskColor.value = '#3b82f6';
                if (taskDate) taskDate.value = '';
                taskColumn.value = btn.dataset.column;

                // Mettre à jour le titre du modal
                if (modalTitle) {
                    modalTitle.textContent = 'Nouvelle tâche';
                }
                if (submitButtonText) {
                    submitButtonText.textContent = 'Créer';
                }

                taskModal.classList.remove('hidden');
                taskTitle.focus();
            });
        });

        // ===== FERMETURE DU MODAL =====
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                closeModal();
            });
        }

        // Fermer en cliquant en dehors
        taskModal.addEventListener('click', (e) => {
            if (e.target === taskModal) {
                closeModal();
            }
        });

        // Fermer avec Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !taskModal.classList.contains('hidden')) {
                closeModal();
            }
        });

        // ===== SOUMISSION DU FORMULAIRE =====
        taskForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const mode = taskMode.value;
            const title = taskTitle.value.trim();
            const category = taskCategory.value.trim();
            const color = taskColor.value;
            const date = taskDate ? taskDate.value : '';
            const column = taskColumn.value;

            if (!title || !column) {
                showNotification('Veuillez remplir le titre de la tâche', 'error');
                return;
            }

            // Désactiver le bouton de soumission
            const submitBtn = document.querySelector('button[form="taskForm"]');
            if (!submitBtn) {
                console.error('❌ Bouton de soumission non trouvé');
                return;
            }

            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enregistrement...';

            // Mode création
            if (mode === 'create') {
                createTask(title, category, color, column, date).finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                });
            }
            // Mode édition
            else if (mode === 'edit' && editTargetId && editTargetId.value) {
                updateTask(editTargetId.value, title, category, color, date).finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                });
            }
        });
    });
} else {
    console.log('ℹ️ task.js ignoré - pas sur une page Kanban');
}

// ===== FONCTIONS UTILITAIRES =====

function closeModal() {
    const taskModal = document.getElementById('taskModal');
    if (taskModal) {
        taskModal.classList.add('hidden');
    }
}

function initializeDragDrop() {
    console.log('🎯 Initialisation du drag & drop');

    // Vérifier si SortableJS est disponible
    if (typeof Sortable === 'undefined') {
        console.warn('⚠️ SortableJS non trouvé - le drag & drop sera désactivé');
        showNotification('Drag & drop non disponible - SortableJS manquant', 'warning');
        return;
    }

    // Sélectionner toutes les colonnes Kanban
    const columns = document.querySelectorAll('.kanban-column-body, ul[id^="column-"]');

    console.log(`📋 ${columns.length} colonnes trouvées pour le drag & drop`);

    if (columns.length === 0) {
        console.warn('⚠️ Aucune colonne Kanban trouvée');
        return;
    }

    columns.forEach((column, index) => {
        try {
            const sortable = new Sortable(column, {
                group: {
                    name: 'kanban-tasks',
                    pull: true,
                    put: true
                },
                animation: 200,
                easing: "cubic-bezier(0.25, 0.46, 0.45, 0.94)",
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                forceFallback: false,
                fallbackClass: 'sortable-fallback',

                // Filtrer les éléments draggables
                filter: '.non-draggable',

                // Événements
                onStart: function (evt) {
                    console.log('🎯 Début du drag:', evt.item.dataset.taskId);

                    // Ajouter des classes visuelles
                    evt.item.classList.add('dragging');
                    document.body.classList.add('is-dragging');

                    // Mettre en évidence les zones de drop
                    document.querySelectorAll('.kanban-column').forEach(col => {
                        col.classList.add('drop-target');
                    });
                },

                onEnd: function (evt) {
                    console.log('🎯 Fin du drag');

                    // Nettoyer les classes visuelles
                    evt.item.classList.remove('dragging');
                    document.body.classList.remove('is-dragging');
                    document.querySelectorAll('.kanban-column').forEach(col => {
                        col.classList.remove('drop-target', 'drop-active');
                    });

                    const item = evt.item;
                    const taskId = item.dataset.taskId;

                    // Déterminer la nouvelle colonne
                    let newColumn = null;

                    // Méthode 1: data-column sur le conteneur
                    if (evt.to.dataset.column) {
                        newColumn = evt.to.dataset.column;
                    }
                    // Méthode 2: ID de la colonne
                    else if (evt.to.id && evt.to.id.startsWith('column-')) {
                        newColumn = evt.to.id.replace('column-', '').replace(/-/g, ' ');
                        // Capitaliser selon les colonnes standard
                        const columnMap = {
                            'backlog': 'Backlog',
                            'to-do': 'To Do',
                            'in-progress': 'In Progress',
                            'to-be-checked': 'To Be Checked',
                            'done': 'Done'
                        };
                        newColumn = columnMap[evt.to.id.replace('column-', '')] || newColumn;
                    }
                    // Méthode 3: chercher dans les parents
                    else {
                        const columnContainer = evt.to.closest('[data-column]');
                        if (columnContainer) {
                            newColumn = columnContainer.dataset.column;
                        }
                    }

                    console.log('🔄 Déplacement de tâche:', {
                        taskId,
                        newColumn,
                        fromColumn: evt.from.dataset.column || evt.from.id,
                        toColumn: evt.to.dataset.column || evt.to.id
                    });

                    // Vérifier si la colonne a vraiment changé
                    const oldColumn = evt.from.dataset.column;
                    if (taskId && newColumn && newColumn !== oldColumn) {
                        updateTaskColumn(taskId, newColumn).catch(error => {
                            console.error('❌ Erreur lors du déplacement:', error);
                            // Remettre la tâche à sa position d'origine
                            evt.from.insertBefore(item, evt.from.children[evt.oldIndex]);
                            showNotification('Erreur lors du déplacement de la tâche', 'error');
                        });
                    } else if (!taskId || !newColumn) {
                        console.error('❌ Impossible de déterminer la nouvelle colonne', {
                            taskId,
                            newColumn,
                            toElement: evt.to,
                            toId: evt.to.id,
                            toDataset: evt.to.dataset
                        });
                        showNotification('Erreur lors du déplacement', 'error');
                    }
                },

                onMove: function (evt) {
                    // Permettre le déplacement uniquement si c'est une tâche
                    const isTask = evt.dragged.classList.contains('task-card') ||
                        evt.dragged.classList.contains('kanban-task') ||
                        evt.dragged.dataset.taskId;

                    if (isTask && evt.to.classList.contains('kanban-column-body')) {
                        evt.to.closest('.kanban-column').classList.add('drop-active');
                    }

                    return isTask;
                },

                onAdd: function (evt) {
                    // Tâche ajoutée à cette colonne
                    updateColumnCounter(evt.to);
                },

                onRemove: function (evt) {
                    // Tâche retirée de cette colonne
                    updateColumnCounter(evt.from);
                }
            });

            console.log(`✅ Drag & drop activé pour colonne ${index + 1}:`, column.id || column.className);

        } catch (error) {
            console.error(`❌ Erreur lors de l'initialisation du drag & drop pour la colonne ${index + 1}:`, error);
        }
    });
}

function updateColumnCounter(columnElement) {
    const column = columnElement.closest('.kanban-column');
    if (column) {
        const counter = column.querySelector('.column-counter');
        const taskCount = columnElement.children.length;
        if (counter) {
            counter.textContent = taskCount;
        }
    }
}

function createTask(title, category, color, column, date = '') {
    console.log('📝 Création de tâche:', { title, category, color, column, date });

    return new Promise((resolve, reject) => {
        // Vérifier que projectId est défini
        if (typeof projectId === 'undefined') {
            console.error('❌ projectId non défini');
            showNotification('Erreur: ID du projet manquant', 'error');
            reject(new Error('projectId non défini'));
            return;
        }

        const taskData = {
            title: title,
            category: category,
            color: color,
            column: column,
            project_id: projectId
        };

        // Ajouter la date si elle existe
        if (date) {
            taskData.due_date = date;
        }

        fetch('/tasks', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(taskData)
        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(response => {
                console.log('✅ Réponse serveur:', response);

                // Le TaskController peut retourner la tâche directement ou dans response.task
                const task = response.task || response;

                if (!task || !task.id) {
                    throw new Error('Réponse serveur invalide');
                }

                // Ajouter la tâche visuellement
                const li = generateTaskCard(task.title, task.category, task.color, task.id, task.due_date);
                const columnEl = findColumnElement(task.column);

                if (columnEl) {
                    columnEl.appendChild(li);
                    attachCardActions(li);

                    // Ajouter la tâche au tableau global
                    if (typeof tasks !== 'undefined' && Array.isArray(tasks)) {
                        tasks.push(task);
                    }

                    // Animation d'entrée
                    li.classList.add('task-enter');
                    setTimeout(() => li.classList.remove('task-enter'), 300);

                    // Mettre à jour le compteur de la colonne
                    updateColumnCounter(columnEl);

                    showNotification('Tâche créée avec succès', 'success');
                    closeModal();
                    resolve(task);
                } else {
                    throw new Error(`Colonne non trouvée: ${task.column}`);
                }
            })
            .catch(error => {
                console.error('❌ Erreur création tâche:', error);

                let errorMessage = 'Erreur lors de la création de la tâche';
                if (error.errors) {
                    errorMessage = Object.values(error.errors).flat().join(', ');
                } else if (error.message) {
                    errorMessage = error.message;
                }

                showNotification(errorMessage, 'error');
                reject(error);
            });
    });
}

function updateTask(taskId, title, category, color, date = '') {
    console.log('📝 Mise à jour de tâche:', { taskId, title, category, color, date });

    return new Promise((resolve, reject) => {
        const updateData = {
            title: title,
            category: category,
            color: color
        };

        // Ajouter la date si elle existe
        if (date) {
            updateData.due_date = date;
        }

        fetch(`/tasks/${taskId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(updateData)
        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(response => {
                console.log('✅ Tâche mise à jour:', response);

                const updatedTask = response.task || response;

                // Mettre à jour la carte visuellement
                const card = document.querySelector(`[data-task-id="${taskId}"]`);
                if (card) {
                    const titleEl = card.querySelector('.task-title');
                    const badge = card.querySelector('.task-badge');
                    const dateEl = card.querySelector('.task-date');

                    if (titleEl) titleEl.textContent = title;

                    if (badge) {
                        badge.textContent = category;
                        badge.style.backgroundColor = color;
                        badge.style.color = getContrastColor(color);
                    }

                    // Mettre à jour la date
                    if (dateEl && date) {
                        dateEl.textContent = formatDate(date);
                        dateEl.style.display = 'block';
                    } else if (dateEl && !date) {
                        dateEl.style.display = 'none';
                    }

                    // Animation de mise à jour
                    card.classList.add('task-updated');
                    setTimeout(() => card.classList.remove('task-updated'), 300);

                    // Mettre à jour la tâche dans le tableau global
                    if (typeof tasks !== 'undefined' && Array.isArray(tasks)) {
                        const taskIndex = tasks.findIndex(t => t.id == taskId);
                        if (taskIndex !== -1) {
                            tasks[taskIndex] = { ...tasks[taskIndex], ...updatedTask };
                        }
                    }
                }

                showNotification('Tâche mise à jour avec succès', 'success');
                closeModal();
                resolve(updatedTask);
            })
            .catch(error => {
                console.error('❌ Erreur mise à jour tâche:', error);

                let errorMessage = 'Erreur lors de la mise à jour';
                if (error.errors) {
                    errorMessage = Object.values(error.errors).flat().join(', ');
                } else if (error.message) {
                    errorMessage = error.message;
                }

                showNotification(errorMessage, 'error');
                reject(error);
            });
    });
}

function updateTaskColumn(taskId, newColumn) {
    console.log('🔄 Déplacement de tâche:', { taskId, newColumn });

    return new Promise((resolve, reject) => {
        fetch(`/tasks/${taskId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ column: newColumn })
        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                console.log('✅ Colonne mise à jour:', data);

                // Mettre à jour la tâche dans le tableau global
                if (typeof tasks !== 'undefined' && Array.isArray(tasks)) {
                    const taskIndex = tasks.findIndex(t => t.id == taskId);
                    if (taskIndex !== -1) {
                        tasks[taskIndex].column = newColumn;
                    }
                }

                // Mettre à jour les compteurs des colonnes
                document.querySelectorAll('.kanban-column-body').forEach(updateColumnCounter);

                showNotification(`Tâche déplacée vers "${newColumn}"`, 'success');
                resolve(data);
            })
            .catch(error => {
                console.error('❌ Erreur mise à jour colonne:', error);

                let errorMessage = 'Erreur lors du déplacement';
                if (error.message) {
                    errorMessage = error.message;
                }

                showNotification(errorMessage, 'error');
                reject(error);
            });
    });
}

function findColumnElement(columnName) {
    // Essayer différents sélecteurs
    const selectors = [
        `#column-${columnName.toLowerCase().replaceAll(' ', '-')}`,
        `[data-column="${columnName}"]`,
        `.kanban-column[data-column="${columnName}"] .kanban-column-body`,
        `.kanban-column[data-column="${columnName}"] ul`
    ];

    for (const selector of selectors) {
        const element = document.querySelector(selector);
        if (element) {
            console.log(`✅ Colonne trouvée avec: ${selector}`);
            return element;
        }
    }

    console.warn(`⚠️ Colonne non trouvée pour: ${columnName}`);
    return null;
}

function loadTasksFromServer() {
    console.log('📥 Chargement des tâches existantes');

    // Vérifier que la variable tasks existe
    if (typeof tasks === 'undefined' || !Array.isArray(tasks)) {
        console.log('ℹ️ Pas de tâches à charger');
        return;
    }

    // Nettoyer d'abord toutes les tâches existantes dans le DOM
    document.querySelectorAll('.task-card').forEach(card => {
        card.remove();
    });

    tasks.forEach(task => {
        const li = generateTaskCard(task.title, task.category, task.color, task.id, task.due_date);
        li.id = `task-${task.id}`;

        const columnEl = findColumnElement(task.column);

        if (columnEl) {
            columnEl.appendChild(li);
            attachCardActions(li);
        } else {
            console.warn('⚠️ Colonne non trouvée pour la tâche:', task);
        }
    });

    // Mettre à jour tous les compteurs
    document.querySelectorAll('.kanban-column-body').forEach(updateColumnCounter);

    console.log(`✅ ${tasks.length} tâches chargées`);
}

function generateTaskCard(title, category, color, id, dueDate = null) {
    const li = document.createElement('li');
    li.className = 'task-card kanban-task';
    li.dataset.taskId = id;
    li.id = `task-${id}`;

    let html = '';

    // Badge de catégorie
    if (category) {
        const textColor = getContrastColor(color || '#6b7280');
        html += `<div class="task-badge" style="background-color: ${color || '#6b7280'}; color: ${textColor}">${category}</div>`;
    }

    // Titre
    html += `<div class="task-title">${escapeHtml(title)}</div>`;

    // Date d'échéance
    if (dueDate) {
        html += `<div class="task-date text-xs text-gray-500 dark:text-gray-400 mb-2">
            <i class="fas fa-calendar mr-1"></i>${formatDate(dueDate)}
        </div>`;
    }

    // Utilisateur (si userInitials est défini)
    if (typeof userInitials !== 'undefined') {
        html += `<div class="task-user text-xs text-gray-500 dark:text-gray-400 mb-2">
            <i class="fas fa-user-circle mr-1"></i>${userInitials}
        </div>`;
    }

    // Actions (modifier/supprimer)
    html += `
        <div class="task-actions">
            <button class="edit-btn" onclick="editTask('${id}')" title="Modifier">
                <i class="fas fa-edit"></i>
            </button>
            <button class="delete-btn" onclick="deleteTask('${id}')" title="Supprimer">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;

    li.innerHTML = html;
    return li;
}

function attachCardActions(cardElement) {
    // Actions déjà attachées via onclick dans generateTaskCard
    console.log('🔗 Actions attachées à la carte:', cardElement.dataset.taskId);
}

// Fonctions globales pour les actions des cartes
window.editTask = function (taskId) {
    console.log('✏️ Édition de tâche:', taskId);

    const taskModal = document.getElementById('taskModal');
    const taskForm = document.getElementById('taskForm');
    const taskTitle = document.getElementById('taskTitle');
    const taskCategory = document.getElementById('taskCategory');
    const taskColor = document.getElementById('taskColor');
    const taskDate = document.getElementById('taskDate');
    const taskMode = document.getElementById('taskMode');
    const editTargetId = document.getElementById('editTargetId');

    if (!taskModal || !taskForm) {
        showNotification('Modal non disponible', 'error');
        return;
    }

    const card = document.querySelector(`[data-task-id="${taskId}"]`);
    if (!card) {
        showNotification('Carte non trouvée', 'error');
        return;
    }

    // Remplir le formulaire avec les données actuelles
    taskMode.value = 'edit';
    editTargetId.value = taskId;

    taskTitle.value = card.querySelector('.task-title').textContent.trim();
    taskCategory.value = card.querySelector('.task-badge')?.textContent.trim() || '';
    taskColor.value = rgbToHex(card.querySelector('.task-badge')?.style.backgroundColor || '#6b7280');

    // Récupérer la date de la tâche
    if (taskDate && typeof tasks !== 'undefined' && Array.isArray(tasks)) {
        const task = tasks.find(t => t.id == taskId);
        if (task && task.due_date) {
            taskDate.value = formatDateForInput(task.due_date);
        } else {
            taskDate.value = '';
        }
    }

    // Mettre à jour le titre du modal
    const modalTitle = document.getElementById('modalTitle');
    const submitButtonText = document.getElementById('submitButtonText');

    if (modalTitle) {
        modalTitle.textContent = 'Modifier la tâche';
    }
    if (submitButtonText) {
        submitButtonText.textContent = 'Sauvegarder';
    }

    // Ouvrir le modal
    taskModal.classList.remove('hidden');
    taskTitle.focus();
};

window.deleteTask = function (taskId) {
    console.log('🗑️ Suppression de tâche:', taskId);

    if (!confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')) {
        return;
    }

    const card = document.querySelector(`[data-task-id="${taskId}"]`);

    fetch(`/tasks/${taskId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            console.log('✅ Tâche supprimée:', data);

            // Supprimer visuellement avec animation
            if (card) {
                card.style.transform = 'scale(0.8)';
                card.style.opacity = '0';
                setTimeout(() => {
                    card.remove();
                    // Mettre à jour le compteur
                    document.querySelectorAll('.kanban-column-body').forEach(updateColumnCounter);
                }, 200);
            }

            // Supprimer du tableau global
            if (typeof tasks !== 'undefined' && Array.isArray(tasks)) {
                const taskIndex = tasks.findIndex(t => t.id == taskId);
                if (taskIndex !== -1) {
                    tasks.splice(taskIndex, 1);
                }
            }

            showNotification('Tâche supprimée avec succès', 'success');
        })
        .catch(error => {
            console.error('❌ Erreur suppression tâche:', error);

            let errorMessage = 'Erreur lors de la suppression';
            if (error.message) {
                errorMessage = error.message;
            }

            showNotification(errorMessage, 'error');
        });
};

window.setTaskColor = function (color) {
    const taskColor = document.getElementById('taskColor');
    if (taskColor) {
        taskColor.value = color;
    }
};

// Fonctions utilitaires
function rgbToHex(rgb) {
    if (!rgb || rgb === 'transparent') return '#6b7280';

    const result = rgb.match(/\d+/g);
    if (!result || result.length < 3) return '#6b7280';

    const [r, g, b] = result;
    return "#" + ((1 << 24) + (parseInt(r) << 16) + (parseInt(g) << 8) + parseInt(b)).toString(16).slice(1);
}

function getContrastColor(hexColor) {
    // Convertir hex en RGB
    const hex = hexColor.replace('#', '');
    const r = parseInt(hex.substr(0, 2), 16);
    const g = parseInt(hex.substr(2, 2), 16);
    const b = parseInt(hex.substr(4, 2), 16);

    // Calculer la luminance
    const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;

    // Retourner blanc ou noir selon la luminance
    return luminance > 0.5 ? '#000000' : '#ffffff';
}

function formatDate(dateString) {
    if (!dateString) return '';

    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    } catch (error) {
        console.warn('Erreur de formatage de date:', error);
        return dateString;
    }
}

function formatDateForInput(dateString) {
    if (!dateString) return '';

    try {
        const date = new Date(dateString);
        return date.toISOString().split('T')[0];
    } catch (error) {
        console.warn('Erreur de formatage de date pour input:', error);
        return '';
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Fonction de notification (utilise celle définie dans le layout principal)
function showNotification(message, type = 'info') {
    if (typeof window.showNotification === 'function') {
        window.showNotification(message, type);
    } else {
        // Fallback si la fonction globale n'est pas disponible
        console.log(`${type.toUpperCase()}: ${message}`);
        alert(message);
    }
}
