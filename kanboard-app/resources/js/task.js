import Sortable from 'sortablejs';

document.addEventListener('DOMContentLoaded', () => {
    // Vérifier si on est sur la page Kanban (pas calendrier)
    const kanbanColumns = document.querySelectorAll('.kanban-column');
    if (kanbanColumns.length === 0) return;
    
    loadTasksFromServer();

    // Initialisation du drag & drop
    document.querySelectorAll('.kanban-column').forEach(column => {
        new Sortable(column, {
            group: 'kanban',
            animation: 150,
            onEnd: function (evt) {
                const item = evt.item;
                const taskId = item.dataset.taskId;
                const newColumn = evt.to.dataset.column;
        
                if (taskId && newColumn) {
                    // 🔁 Envoyer la mise à jour au serveur
                    fetch(`/tasks/${taskId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({ column: newColumn })
                    })
                    .then(res => res.json())
                    .then(data => {
                        console.log('Colonne mise à jour:', data);
                    })
                    .catch(err => console.error(err));
                }
            }
        });
        
    });

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

    // Ouvre la modal depuis un bouton "Ajouter une tâche"
    document.querySelectorAll('.add-task-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            taskMode.value = 'create';
            taskTitle.value = '';
            taskCategory.value = '';
            taskColor.value = '#1e40af'; // Bleu par défaut
            taskDate.value = '';
            taskColumn.value = btn.dataset.column;
            taskModal.classList.remove('hidden');
        });
    });

    // Ferme la modal en cliquant sur "Annuler"
    cancelBtn.addEventListener('click', () => {
        taskModal.classList.add('hidden');
    });

    // Ferme la modal si clic en dehors du formulaire
    taskModal.addEventListener('click', (e) => {
        if (e.target === taskModal) {
            taskModal.classList.add('hidden');
        }
    });

    // Soumission du formulaire
    taskForm.addEventListener('submit', (e) => {
        e.preventDefault();
    
        const mode = taskMode.value;
        const title = taskTitle.value.trim();
        const category = taskCategory.value.trim();
        const color = taskColor.value;
        const date = taskDate.value;
        const column = taskColumn.value;
    
        if (!title || !category || !column) return;
    
        // Mode création
        if (mode === 'create') {
            const taskData = {
                title: title,
                category: category,
                color: color,
                due_date: date,
                column: column,
                project_id: projectId
            };
            
            fetch('/tasks', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(taskData)
            })
            .then(res => res.json())
            .then(task => {
                const li = generateTaskCard(task.title, task.category, task.color, task.id);
                document.querySelector(`#column-${task.column.toLowerCase().replaceAll(' ', '-')}`).appendChild(li);
                attachCardActions(li);
                
                // Ajouter la tâche au tableau global
                tasks.push(task);
            })
            .catch(err => console.error('Erreur enregistrement tâche :', err));
        }
    
        // Mode édition
        else if (mode === 'edit') {
            const taskId = editTargetId.value;
            fetch(`/tasks/${taskId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    title: title,
                    category: category,
                    color: color,
                    due_date: date,
                    column: column
                })
            })
            .then(res => {
                if (!res.ok) throw new Error('Erreur serveur');
                return res.json();
            })
            .then(updatedTask => {
                // Mettre à jour la carte visuellement
                const card = document.getElementById(`task-${taskId}`);
                if (card) {
                    card.querySelector('.task-title').textContent = title;
                    const badge = card.querySelector('.task-badge');
                    badge.textContent = category;
                    badge.style.backgroundColor = color;
                    badge.style.color = '#fff';
                } else {
                    alert('Carte non trouvée dans le DOM !');
                }
                // Mettre à jour la tâche dans le tableau global
                const taskIndex = tasks.findIndex(t => t.id == taskId);
                if (taskIndex !== -1) {
                    tasks[taskIndex] = updatedTask;
                }
            })
            .catch(err => {
                alert('Erreur lors de la mise à jour : ' + err.message);
                console.error('Erreur mise à jour tâche :', err);
            });
        }
    
        taskModal.classList.add('hidden');
    });

    // Charge les tâches depuis le serveur
    function loadTasksFromServer() {
        if (!Array.isArray(tasks)) return;
    
        tasks.forEach(task => {
            const li = generateTaskCard(task.title, task.category, task.color, task.id);
            li.id = `task-${task.id}`;
            document.querySelector(`#column-${task.column.toLowerCase().replaceAll(' ', '-')}`).appendChild(li);
            attachCardActions(li);
        });
    }
    

    // Créer une tâche dans une des colonnes du kanban
    function generateTaskCard(title, category, color, taskId) {
        const li = document.createElement('li');
        li.id = `task-${taskId}`;
        li.dataset.taskId = taskId;
        li.className = 'task-card';
        li.innerHTML = `
            <div>
                <div class="task-actions">
                    <button class="edit-btn">✏️</button>
                    <button class="delete-btn">🗑️</button>
                </div>
                <div class="task-badge" style="background-color: ${color};">${category}</div>
                <div class="task-title">${title}</div>
                <div class="task-user">${userInitials}</div>
            </div>
        `;
    
        return li;
    }

    // Active les boutons modifier/supprimer
    function attachCardActions(card) {
        const editBtn = card.querySelector('.edit-btn');
        const deleteBtn = card.querySelector('.delete-btn');

        editBtn.addEventListener('click', () => {
            taskMode.value = 'edit';
            editTargetId.value = card.dataset.taskId;

            taskTitle.value = card.querySelector('.task-title').textContent.trim();
            taskCategory.value = card.querySelector('.task-badge').textContent.trim();
            taskColor.value = rgbToHex(card.querySelector('.task-badge').style.backgroundColor);
            
            // Récupérer la tâche pour obtenir la date
            const taskId = card.dataset.taskId;
            const task = tasks.find(t => t.id == taskId);
            if (task && task.due_date) {
                let d = new Date(task.due_date);
                if (!isNaN(d)) {
                    // Si c'est une vraie date JS
                    taskDate.value = d.toISOString().split('T')[0];
                } else if (/^\d{4}-\d{2}-\d{2}/.test(task.due_date)) {
                    // Si c'est déjà au bon format
                    taskDate.value = task.due_date;
                } else {
                    taskDate.value = '';
                }
            } else {
                taskDate.value = '';
            }

            taskModal.classList.remove('hidden');
        });

        deleteBtn.addEventListener('click', () => {
            if (confirm("Supprimer cette tâche ?")) {
                const taskId = card.dataset.taskId;
                fetch(`/tasks/${taskId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                })
                .then(res => {
                    if (res.ok) card.remove();
                    else console.error("Erreur suppression");
                })
                .catch(err => console.error(err));
            }
        });
        
    }

    // Utilitaire : conversion RGB → HEX
    function rgbToHex(rgb) {
        const result = rgb.match(/\d+/g);
        return "#" + result.map(x => parseInt(x).toString(16).padStart(2, '0')).join('');
    }
});
