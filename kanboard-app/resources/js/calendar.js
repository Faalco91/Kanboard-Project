class Calendar {
    constructor() {
        this.currentDate = new Date();
        this.currentView = 'day';
        this.container = document.getElementById('calendarContainer');
        this.currentDateElement = document.getElementById('currentDate');
        
        this.initializeEventListeners();
        this.render();
    }

    initializeEventListeners() {
        // Navigation
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const todayBtn = document.getElementById('todayBtn');
        
        if (prevBtn) prevBtn.addEventListener('click', () => this.previous());
        if (nextBtn) nextBtn.addEventListener('click', () => this.next());
        if (todayBtn) todayBtn.addEventListener('click', () => this.goToToday());

        // Changement de vue
        const dayView = document.getElementById('dayView');
        const weekView = document.getElementById('weekView');
        const monthView = document.getElementById('monthView');
        
        if (dayView) dayView.addEventListener('click', () => this.changeView('day'));
        if (weekView) weekView.addEventListener('click', () => this.changeView('week'));
        if (monthView) monthView.addEventListener('click', () => this.changeView('month'));

        // Modal
        const cancelBtn = document.getElementById('cancelTaskModal');
        const taskForm = document.getElementById('taskForm');
        
        if (cancelBtn) cancelBtn.addEventListener('click', () => this.closeModal());
        if (taskForm) taskForm.addEventListener('submit', (e) => this.handleTaskSubmit(e));
        
        // Fermer modal en cliquant à l'extérieur
        const taskModal = document.getElementById('taskModal');
        if (taskModal) {
            taskModal.addEventListener('click', (e) => {
                if (e.target.id === 'taskModal') {
                    this.closeModal();
                }
            });
        }

        // Initialiser le drag & drop après le rendu
        this.initializeDragAndDrop();
    }

    initializeDragAndDrop() {
        // Rendre les tâches draggables
        const tasks = document.querySelectorAll('.calendar-task');
        tasks.forEach(task => {
            task.draggable = true;
            task.addEventListener('dragstart', (e) => this.handleDragStart(e));
            task.addEventListener('dragend', (e) => this.handleDragEnd(e));
        });

        // Rendre les zones de drop
        const dropZones = document.querySelectorAll('.day-content, .week-day, .month-day');
        dropZones.forEach(zone => {
            zone.classList.add('drop-zone', 'clickable');
            zone.addEventListener('dragover', (e) => this.handleDragOver(e));
            zone.addEventListener('dragleave', (e) => this.handleDragLeave(e));
            zone.addEventListener('drop', (e) => this.handleDrop(e));
            zone.addEventListener('click', (e) => this.handleZoneClick(e));
        });
    }

    handleDragStart(e) {
        e.dataTransfer.setData('text/plain', e.target.dataset.taskId);
        e.target.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
    }

    handleDragEnd(e) {
        e.target.classList.remove('dragging');
        // Nettoyer les zones de drop
        document.querySelectorAll('.drag-over').forEach(zone => {
            zone.classList.remove('drag-over');
        });
    }

    handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        e.currentTarget.classList.add('drag-over');
    }

    handleDragLeave(e) {
        e.currentTarget.classList.remove('drag-over');
    }

    async handleDrop(e) {
        e.preventDefault();
        e.currentTarget.classList.remove('drag-over');
        
        const taskId = e.dataTransfer.getData('text/plain');
        const newDate = e.currentTarget.dataset.date;
        const newHour = e.currentTarget.dataset.hour;
        
        if (taskId && newDate) {
            await this.moveTask(taskId, newDate, newHour);
        }
    }

    handleZoneClick(e) {
        // Éviter de déclencher si on clique sur une tâche
        if (e.target.classList.contains('calendar-task')) {
            return;
        }
        
        const date = e.currentTarget.dataset.date;
        const hour = e.currentTarget.dataset.hour;
        this.openTaskModal(date, hour);
    }

    async moveTask(taskId, newDate, newHour) {
        try {
            const response = await fetch(`/tasks/${taskId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    due_date: newDate,
                    hour: newHour
                })
            });
            
            if (response.ok) {
                // Mettre à jour la tâche localement
                const updatedTask = await response.json();
                const taskIndex = tasks.findIndex(t => t.id == taskId);
                if (taskIndex !== -1) {
                    tasks[taskIndex] = updatedTask;
                }
                this.render();
            }
        } catch (error) {
            console.error('Erreur lors du déplacement de la tâche:', error);
        }
    }

    changeView(view) {
        this.currentView = view;
        
        // Mettre à jour les boutons
        const dayViewBtn = document.getElementById('dayView');
        const weekViewBtn = document.getElementById('weekView');
        const monthViewBtn = document.getElementById('monthView');
        
        if (dayViewBtn && weekViewBtn && monthViewBtn) {
            [dayViewBtn, weekViewBtn, monthViewBtn].forEach(btn => {
                btn.classList.remove('bg-blue-100', 'text-blue-700', 'font-medium');
                btn.classList.add('hover:bg-gray-100');
            });
            
            const currentBtn = document.getElementById(view + 'View');
            if (currentBtn) {
                currentBtn.classList.add('bg-blue-100', 'text-blue-700', 'font-medium');
                currentBtn.classList.remove('hover:bg-gray-100');
            }
        }
        
        this.render();
    }

    previous() {
        switch(this.currentView) {
            case 'day':
                this.currentDate.setDate(this.currentDate.getDate() - 1);
                break;
            case 'week':
                this.currentDate.setDate(this.currentDate.getDate() - 7);
                break;
            case 'month':
                this.currentDate.setMonth(this.currentDate.getMonth() - 1);
                break;
        }
        this.render();
    }

    next() {
        switch(this.currentView) {
            case 'day':
                this.currentDate.setDate(this.currentDate.getDate() + 1);
                break;
            case 'week':
                this.currentDate.setDate(this.currentDate.getDate() + 7);
                break;
            case 'month':
                this.currentDate.setMonth(this.currentDate.getMonth() + 1);
                break;
        }
        this.render();
    }

    goToToday() {
        this.currentDate = new Date();
        this.render();
    }

    render() {
        this.updateHeader();
        
        switch(this.currentView) {
            case 'day':
                this.renderDayView();
                break;
            case 'week':
                this.renderWeekView();
                break;
            case 'month':
                this.renderMonthView();
                break;
        }

        // Réinitialiser le drag & drop après le rendu
        this.initializeDragAndDrop();
    }

    updateHeader() {
        const options = { 
            year: 'numeric', 
            month: 'long' 
        };
        
        if (this.currentView === 'day') {
            options.day = 'numeric';
        }
        
        if (this.currentDateElement) {
            this.currentDateElement.textContent = this.currentDate.toLocaleDateString('fr-FR', options);
        }
    }

    renderDayView() {
        const dayStart = new Date(this.currentDate);
        dayStart.setHours(0, 0, 0, 0);
        
        let html = '<div class="day-view">';
        
        // Créer les créneaux horaires (8h-20h)
        for (let hour = 8; hour <= 20; hour++) {
            const timeSlot = new Date(dayStart);
            timeSlot.setHours(hour);
            
            html += `
                <div class="time-slot">
                    ${hour.toString().padStart(2, '0')}:00
                </div>
                <div class="day-content" data-hour="${hour}" onclick="calendar.openTaskModal('${this.formatDateForInput(timeSlot)}', ${hour})">
                    ${this.getTasksForDate(this.currentDate)}
                </div>
            `;
        }
        
        html += '</div>';
        this.container.innerHTML = html;
    }

    renderWeekView() {
        const weekStart = this.getWeekStart(this.currentDate);
        let html = '<div class="week-view">';
        
        // En-têtes des jours
        html += '<div class="week-header">Heure</div>';
        for (let i = 0; i < 7; i++) {
            const date = new Date(weekStart);
            date.setDate(weekStart.getDate() + i);
            const isToday = this.isToday(date);
            
            html += `
                <div class="week-header ${isToday ? 'today' : ''}">
                    ${date.toLocaleDateString('fr-FR', { weekday: 'short', day: 'numeric' })}
                </div>
            `;
        }
        
        // Créneaux horaires
        for (let hour = 8; hour <= 20; hour++) {
            html += `<div class="time-slot">${hour.toString().padStart(2, '0')}:00</div>`;
            
            for (let day = 0; day < 7; day++) {
                const date = new Date(weekStart);
                date.setDate(weekStart.getDate() + day);
                const isToday = this.isToday(date);
                
                html += `
                    <div class="week-day ${isToday ? 'today' : ''}" 
                         data-date="${this.formatDateForInput(date)}" 
                         data-hour="${hour}"
                         onclick="calendar.openTaskModal('${this.formatDateForInput(date)}', ${hour})">
                        ${this.getTasksForDate(date)}
                    </div>
                `;
            }
        }
        
        html += '</div>';
        this.container.innerHTML = html;
    }

    renderMonthView() {
        const monthStart = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1);
        const monthEnd = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 0);
        const weekStart = this.getWeekStart(monthStart);
        
        let html = '<div class="month-view">';
        
        // En-têtes des jours de la semaine
        const daysOfWeek = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
        daysOfWeek.forEach(day => {
            html += `<div class="month-header">${day}</div>`;
        });
        
        // Jours du mois
        let currentDate = new Date(weekStart);
        const endDate = new Date(weekStart);
        endDate.setDate(weekStart.getDate() + 41); // 6 semaines
        
        while (currentDate < endDate) {
            const isToday = this.isToday(currentDate);
            const isOtherMonth = currentDate.getMonth() !== this.currentDate.getMonth();
            const dayNumber = currentDate.getDate();
            
            html += `
                <div class="month-day ${isToday ? 'today' : ''} ${isOtherMonth ? 'other-month' : ''}" 
                     data-date="${this.formatDateForInput(currentDate)}"
                     onclick="calendar.openTaskModal('${this.formatDateForInput(currentDate)}')">
                    <div class="day-number">${dayNumber}</div>
                    ${this.getTasksForDate(currentDate)}
                </div>
            `;
            
            currentDate.setDate(currentDate.getDate() + 1);
        }
        
        html += '</div>';
        this.container.innerHTML = html;
    }

    getWeekStart(date) {
        const day = date.getDay();
        const diff = date.getDate() - day;
        return new Date(date.setDate(diff));
    }

    isToday(date) {
        const today = new Date();
        return date.toDateString() === today.toDateString();
    }

    formatDateForInput(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    getTasksForDate(date) {
        const dateStr = this.formatDateForInput(date);
        
        const dayTasks = tasks.filter(task => {
            // Si la tâche a une date d'échéance, filtrer par date
            if (task.due_date) {
                const taskDate = this.formatDateForInput(new Date(task.due_date));
                return taskDate === dateStr;
            }
            // Si pas de date, afficher toutes les tâches (comportement par défaut)
            return true;
        });
        
        return dayTasks.slice(0, 3).map(task => `
            <div class="calendar-task ${this.getTaskClass(task.column)}" 
                 data-task-id="${task.id}"
                 onclick="calendar.editTask(${task.id})">
                ${task.title}
            </div>
        `).join('');
    }

    getTasksForDateAndHour(date, hour) {
        const dateStr = this.formatDateForInput(date);
        const dayTasks = tasks.filter(task => {
            // Filtrer les tâches par date d'échéance
            if (task.due_date) {
                return task.due_date === dateStr;
            }
            // Si pas de date, afficher toutes les tâches (comportement par défaut)
            return true;
        });
        
        return dayTasks.slice(0, 2).map(task => `
            <div class="calendar-task ${this.getTaskClass(task.column)}" 
                 data-task-id="${task.id}"
                 onclick="calendar.editTask(${task.id})">
                ${task.title}
            </div>
        `).join('');
    }

    getTaskClass(column) {
        switch(column) {
            case 'Done':
                return 'done';
            case 'In Progress':
                return 'in-progress';
            case 'Backlog':
                return 'backlog';
            default:
                return '';
        }
    }

    openTaskModal(date = null, hour = null) {
        const modal = document.getElementById('taskModal');
        const dateInput = document.getElementById('taskDate');
        const modeInput = document.getElementById('taskMode');
        
        if (date) {
            dateInput.value = date;
        } else {
            dateInput.value = this.formatDateForInput(new Date());
        }
        
        modeInput.value = 'create';
        document.getElementById('taskModalTitle').textContent = 'Nouvelle tâche';
        
        // Réinitialiser le formulaire
        document.getElementById('taskForm').reset();
        dateInput.value = date || this.formatDateForInput(new Date());
        document.getElementById('taskColor').value = '#3B82F6';
        
        modal.classList.remove('hidden');
    }

    editTask(taskId) {
        const task = tasks.find(t => t.id === taskId);
        if (!task) return;
        
        const modal = document.getElementById('taskModal');
        const modeInput = document.getElementById('taskMode');
        const editTargetInput = document.getElementById('editTargetId');
        
        modeInput.value = 'edit';
        editTargetInput.value = taskId;
        document.getElementById('taskModalTitle').textContent = 'Modifier la tâche';
        
        // Remplir le formulaire
        document.getElementById('taskTitle').value = task.title;
        document.getElementById('taskCategory').value = task.category;
        document.getElementById('taskColor').value = task.color;
        document.getElementById('taskColumn').value = task.column;
        document.getElementById('taskDate').value = task.due_date || '';
        
        modal.classList.remove('hidden');
    }

    closeModal() {
        document.getElementById('taskModal').classList.add('hidden');
    }

    async handleTaskSubmit(e) {
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
            if (mode === 'create') {
                const response = await fetch('/tasks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(taskData)
                });
                
                if (response.ok) {
                    const newTask = await response.json();
                    tasks.push(newTask);
                    this.render();
                }
            } else if (mode === 'edit') {
                const taskId = formData.get('editTargetId');
                const response = await fetch(`/tasks/${taskId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(taskData)
                });
                
                if (response.ok) {
                    const updatedTask = await response.json();
                    const taskIndex = tasks.findIndex(t => t.id == taskId);
                    if (taskIndex !== -1) {
                        tasks[taskIndex] = updatedTask;
                    }
                    this.render();
                }
            }
            
            this.closeModal();
        } catch (error) {
            console.error('Erreur lors de la sauvegarde:', error);
        }
    }
}

// Initialiser le calendrier quand le DOM est chargé
let calendar;
document.addEventListener('DOMContentLoaded', () => {
    // Vérifier si on est sur la page calendrier
    const calendarContainer = document.getElementById('calendarContainer');
    if (calendarContainer) {
        calendar = new Calendar();
    }
}); 