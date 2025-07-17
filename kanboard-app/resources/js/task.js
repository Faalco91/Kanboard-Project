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

        // Initialiser Sortable pour le drag & drop (si disponible)
        if (typeof Sortable !== 'undefined') {
            initializeDragDrop();
        }

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
                taskModal.classList.remove('hidden');
            });
        });

        // ===== FERMETURE DU MODAL =====
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                taskModal.classList.add('hidden');
            });
        }

        // Fermer en cliquant en dehors
        taskModal.addEventListener('click', (e) => {
            if (e.target === taskModal) {
                taskModal.classList.add('hidden');
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
                console.warn('⚠️ Titre ou colonne manquant');
                return;
            }

            // Mode création
            if (mode === 'create') {
                createTask(title, category, color, column, date);
            }
            // Mode édition
            else if (mode === 'edit' && editTargetId) {
                updateTask(editTargetId.value, title, category, color, date);
            }

            taskModal.classList.add('hidden');
        });
    });
} else {
    console.log('ℹ️ task.js ignoré - pas sur une page Kanban');
}

// ===== FONCTIONS UTILITAIRES =====

function initializeDragDrop() {
    console.log('🎯 Initialisation du drag & drop');

    document.querySelectorAll('.kanban-column').forEach(column => {
        new Sortable(column, {
            group: 'kanban',
            animation: 150,
            onEnd: function (evt) {
                const item = evt.item;
                const taskId = item.dataset.taskId;
                const newColumn = evt.to.dataset.column;

                if (taskId && newColumn) {
                    updateTaskColumn(taskId, newColumn);
                }
            }
        });
    });
}

function createTask(title, category, color, column, date = '') {
    console.log('📝 Création de tâche:', { title, category, color, column, date });

    // Vérifier que projectId est défini
    if (typeof projectId === 'undefined') {
        console.error('❌ projectId non défini');
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
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify(taskData)
    })
        .then(res => res.json())
        .then(response => {
            console.log('✅ Tâche créée:', response);

            const task = response.task || response;
            
            // Ajouter la tâche visuellement
            const li = generateTaskCard(task.title, task.category, task.color, task.id);
            const columnEl = document.querySelector(`#column-${task.column.toLowerCase().replaceAll(' ', '-')}`);

            if (columnEl) {
                columnEl.appendChild(li);
                attachCardActions(li);
                
                // Ajouter la tâche au tableau global
                if (typeof tasks !== 'undefined' && Array.isArray(tasks)) {
                    tasks.push(task);
                }
            }
        })
        .catch(err => {
            console.error('❌ Erreur création tâche:', err);
            alert('Erreur lors de la création de la tâche');
        });
}

function updateTask(taskId, title, category, color, date = '') {
    console.log('📝 Mise à jour de tâche:', { taskId, title, category, color, date });

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
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify(updateData)
    })
        .then(res => {
            if (!res.ok) throw new Error('Erreur serveur');
            return res.json();
        })
        .then(response => {
            console.log('✅ Tâche mise à jour:', response);

            const updatedTask = response.task || response;
            
            // Mettre à jour la carte visuellement
            const card = document.querySelector(`[data-task-id="${taskId}"]`);
            if (card) {
                const titleEl = card.querySelector('.task-title');
                const badge = card.querySelector('.task-badge');

                if (titleEl) titleEl.textContent = title;
                if (badge) {
                    badge.textContent = category;
                    badge.style.backgroundColor = color;
                    badge.style.color = '#fff';
                }

                // Mettre à jour la tâche dans le tableau global
                if (typeof tasks !== 'undefined' && Array.isArray(tasks)) {
                    const taskIndex = tasks.findIndex(t => t.id == taskId);
                    if (taskIndex !== -1) {
                        tasks[taskIndex] = updatedTask;
                    }
                }
            }
        })
        .catch(err => {
            console.error('❌ Erreur mise à jour tâche:', err);
            alert('Erreur lors de la mise à jour : ' + err.message);
        });
}

function updateTaskColumn(taskId, newColumn) {
    console.log('🔄 Déplacement de tâche:', { taskId, newColumn });

    fetch(`/tasks/${taskId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        },
        body: JSON.stringify({ column: newColumn })
    })
        .then(res => res.json())
        .then(data => {
            console.log('✅ Colonne mise à jour:', data);
        })
        .catch(err => {
            console.error('❌ Erreur mise à jour colonne:', err);
        });
}

function loadTasksFromServer() {
    console.log('📥 Chargement des tâches existantes');

    // Vérifier que la variable tasks existe
    if (typeof tasks === 'undefined' || !Array.isArray(tasks)) {
        console.log('ℹ️ Pas de tâches à charger');
        return;
    }

    tasks.forEach(task => {
        const li = generateTaskCard(task.title, task.category, task.color, task.id);
        li.id = `task-${task.id}`;

        const columnSelector = `#column-${task.column.toLowerCase().replaceAll(' ', '-')}`;
        const columnEl = document.querySelector(columnSelector);

        if (columnEl) {
            columnEl.appendChild(li);
            attachCardActions(li);
        } else {
            console.warn('⚠️ Colonne non trouvée:', columnSelector);
        }
    });

    console.log(`✅ ${tasks.length} tâches chargées`);
}

function generateTaskCard(title, category, color, id) {
    const li = document.createElement('li');
    li.className = 'task-card';
    li.dataset.taskId = id;
    li.id = `task-${id}`;

    let html = '';

    // Badge de catégorie
    if (category) {
        html += `<div class="task-badge" style="background-color: ${color || '#6b7280'}">${category}</div>`;
    }

    // Titre
    html += `<div class="task-title">${title}</div>`;

    // Utilisateur (si userInitials est défini)
    if (typeof userInitials !== 'undefined') {
        html += `<div class="task-user">${userInitials}</div>`;
    }

    // Actions (modifier/supprimer)
    html += `
        <div class="task-actions">
            <button class="edit-btn" onclick="editTask('${id}')" title="Modifier">✏️</button>
            <button class="delete-btn" onclick="deleteTask('${id}')" title="Supprimer">🗑️</button>
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
        alert('Modal non disponible');
        return;
    }

    const card = document.querySelector(`[data-task-id="${taskId}"]`);
    if (!card) {
        alert('Carte non trouvée');
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
            let d = new Date(task.due_date);
            if (!isNaN(d)) {
                taskDate.value = d.toISOString().split('T')[0];
            } else if (/^\d{4}-\d{2}-\d{2}/.test(task.due_date)) {
                taskDate.value = task.due_date;
            } else {
                taskDate.value = '';
            }
        } else {
            taskDate.value = '';
        }
    }

    // Ouvrir le modal
    taskModal.classList.remove('hidden');
};

window.deleteTask = function (taskId) {
    console.log('🗑️ Suppression de tâche:', taskId);

    if (confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')) {
        fetch(`/tasks/${taskId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            }
        })
            .then(res => res.json())
            .then(data => {
                console.log('✅ Tâche supprimée:', data);
                
                // Supprimer visuellement
                const card = document.querySelector(`[data-task-id="${taskId}"]`);
                if (card) {
                    card.remove();
                }

                // Supprimer du tableau global
                if (typeof tasks !== 'undefined' && Array.isArray(tasks)) {
                    const taskIndex = tasks.findIndex(t => t.id == taskId);
                    if (taskIndex !== -1) {
                        tasks.splice(taskIndex, 1);
                    }
                }
            })
            .catch(err => {
                console.error('❌ Erreur suppression tâche:', err);
                alert('Erreur lors de la suppression');
            });
    }
};

// Fonction utilitaire pour convertir RGB en HEX
function rgbToHex(rgb) {
    if (!rgb || rgb === 'transparent') return '#6b7280';
    
    const result = rgb.match(/\d+/g);
    if (!result) return '#6b7280';
    
    const [r, g, b] = result;
    return "#" + ((1 << 24) + (parseInt(r) << 16) + (parseInt(g) << 8) + parseInt(b)).toString(16).slice(1);
}