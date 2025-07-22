<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ $project->name }} - Vue Calendrier
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ $calendarTasks->count() }} tâches avec date d'échéance
                </p>
            </div>
            
            {{-- Navigation des vues --}}
            <nav class="flex flex-col sm:flex-row w-full sm:w-auto gap-2 sm:gap-4">
                <a href="{{ route('projects.show', $project) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium text-center text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors">
                    <i class="fas fa-columns mr-2"></i>Kanban
                </a>
                
                <a href="{{ route('projects.list', $project) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium text-center text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors">
                    <i class="fas fa-list mr-2"></i>Liste
                </a>
                
                <a href="{{ route('projects.calendar', $project) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium text-center bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                    <i class="fas fa-calendar mr-2"></i>Calendrier
                </a>
            </nav>
        </div>
    </x-slot>

    @push('styles')
        {{-- FullCalendar CSS --}}
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
        <style>
            /* ===== PERSONNALISATION CALENDRIER DARK MODE ===== */
            .fc {
                background: white;
                border-radius: 0.75rem;
                overflow: hidden;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
                border: 1px solid #e5e7eb;
            }
            
            /* Dark mode pour le calendrier */
            .dark .fc {
                background: #1f2937;
                border-color: #374151;
                color: #f9fafb;
            }
            
            /* En-têtes du calendrier */
            .fc .fc-toolbar {
                background: #f8fafc;
                padding: 1rem;
                border-bottom: 1px solid #e5e7eb;
            }
            
            .dark .fc .fc-toolbar {
                background: #111827;
                border-color: #374151;
            }
            
            .fc .fc-toolbar-title {
                color: #1f2937;
                font-weight: 600;
            }
            
            .dark .fc .fc-toolbar-title {
                color: #f9fafb;
            }
            
            /* Boutons de navigation */
            .fc .fc-button {
                background: #3b82f6;
                border-color: #3b82f6;
                color: white;
                border-radius: 0.5rem;
                padding: 0.5rem 1rem;
                font-weight: 500;
            }
            
            .fc .fc-button:hover {
                background: #2563eb;
                border-color: #2563eb;
            }
            
            .fc .fc-button:disabled {
                background: #9ca3af;
                border-color: #9ca3af;
                opacity: 0.5;
            }
            
            .dark .fc .fc-button:disabled {
                background: #4b5563;
                border-color: #4b5563;
            }
            
            /* En-têtes des jours */
            .fc .fc-col-header {
                background: #f8fafc;
                border-bottom: 1px solid #e5e7eb;
            }
            
            .dark .fc .fc-col-header {
                background: #111827;
                border-color: #374151;
            }
            
            .fc .fc-col-header-cell {
                padding: 0.75rem 0.5rem;
                font-weight: 600;
                color: #374151;
            }
            
            .dark .fc .fc-col-header-cell {
                color: #d1d5db;
            }
            
            /* Cellules des jours */
            .fc .fc-daygrid-day {
                background: white;
                border-color: #f3f4f6;
                min-height: 120px;
            }
            
            .dark .fc .fc-daygrid-day {
                background: #1f2937;
                border-color: #374151;
            }
            
            .fc .fc-daygrid-day:hover {
                background: #f8fafc;
            }
            
            .dark .fc .fc-daygrid-day:hover {
                background: #374151;
            }
            
            /* Numéros des jours */
            .fc .fc-daygrid-day-number {
                color: #374151;
                font-weight: 500;
                padding: 0.5rem;
            }
            
            .dark .fc .fc-daygrid-day-number {
                color: #d1d5db;
            }
            
            /* Jour aujourd'hui */
            .fc .fc-day-today {
                background: #eff6ff !important;
            }
            
            .dark .fc .fc-day-today {
                background: #1e3a8a !important;
            }
            
            .fc .fc-day-today .fc-daygrid-day-number {
                background: #3b82f6;
                color: white;
                border-radius: 50%;
                width: 2rem;
                height: 2rem;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0.25rem;
            }
            
            /* Événements/Tâches */
            .fc .fc-event {
                border-radius: 0.375rem;
                border: none;
                padding: 0.25rem 0.5rem;
                margin: 0.125rem;
                font-size: 0.75rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            
            .fc .fc-event:hover {
                transform: scale(1.02);
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            }
            
            .fc .fc-event-title {
                font-weight: 500;
            }
            
            /* Couleurs spécifiques par statut */
            .fc .fc-event.status-backlog {
                background: #6b7280;
                color: white;
            }
            
            .fc .fc-event.status-todo {
                background: #3b82f6;
                color: white;
            }
            
            .fc .fc-event.status-progress {
                background: #f59e0b;
                color: white;
            }
            
            .fc .fc-event.status-check {
                background: #8b5cf6;
                color: white;
            }
            
            .fc .fc-event.status-done {
                background: #10b981;
                color: white;
            }
            
            /* Vue semaine et jour */
            .fc .fc-timegrid-slot {
                height: 3rem;
                border-color: #f3f4f6;
            }
            
            .dark .fc .fc-timegrid-slot {
                border-color: #374151;
            }
            
            .fc .fc-timegrid-axis {
                background: #f8fafc;
                border-color: #e5e7eb;
            }
            
            .dark .fc .fc-timegrid-axis {
                background: #111827;
                border-color: #374151;
                color: #d1d5db;
            }
            
            /* Responsive */
            @media (max-width: 768px) {
                .fc .fc-toolbar {
                    flex-direction: column;
                    gap: 0.5rem;
                }
                
                .fc .fc-toolbar-chunk {
                    display: flex;
                    justify-content: center;
                }
                
                .fc .fc-button {
                    padding: 0.375rem 0.75rem;
                    font-size: 0.875rem;
                }
            }
            
            /* Animations */
            .fc .fc-event {
                animation: eventFadeIn 0.3s ease-out;
            }
            
            @keyframes eventFadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            /* Bordures globales pour le dark mode */
            .dark .fc td,
            .dark .fc th {
                border-color: #374151;
            }
            
            .dark .fc .fc-scrollgrid {
                border-color: #374151;
            }
            
            /* Personnalisation du contenu des cellules */
            .fc .fc-daygrid-day-events {
                margin: 0.25rem;
            }
            
            .fc .fc-daygrid-event-harness {
                margin-bottom: 0.125rem;
            }
            
            /* Plus d'événements */
            .fc .fc-daygrid-more-link {
                background: #f3f4f6;
                color: #6b7280;
                border-radius: 0.25rem;
                padding: 0.125rem 0.375rem;
                font-size: 0.75rem;
                text-decoration: none;
                margin: 0.125rem;
            }
            
            .dark .fc .fc-daygrid-more-link {
                background: #4b5563;
                color: #d1d5db;
            }
            
            .fc .fc-daygrid-more-link:hover {
                background: #e5e7eb;
                color: #374151;
            }
            
            .dark .fc .fc-daygrid-more-link:hover {
                background: #6b7280;
                color: #f3f4f6;
            }
        </style>
    @endpush

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Messages de succès/erreur --}}
            @if(session('success'))
                <div class="mb-6 bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <div class="flex">
                        <i class="fas fa-check-circle text-green-400 mr-3 mt-0.5"></i>
                        <p class="text-green-800 dark:text-green-200">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            {{-- Contrôles de vue --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Calendrier des tâches
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Visualisez vos tâches en fonction de leur date d'échéance
                        </p>
                    </div>
                    
                    <div class="flex gap-2">
                        <button id="dayViewBtn" class="px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            Jour
                        </button>
                        <button id="weekViewBtn" class="px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            Semaine
                        </button>
                        <button id="monthViewBtn" class="px-3 py-2 text-sm bg-blue-600 text-white rounded-lg">
                            Mois
                        </button>
                    </div>
                </div>
            </div>

            {{-- Calendrier --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    {{-- Modal de détail de tâche --}}
    <div id="taskDetailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100" id="modalTaskTitle">
                    Détails de la tâche
                </h3>
            </div>
            
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Statut</label>
                    <span id="modalTaskStatus" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                        En cours
                    </span>
                </div>
                
                <div id="modalTaskCategoryContainer" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catégorie</label>
                    <span id="modalTaskCategory" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white">
                        Développement
                    </span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assigné à</label>
                    <p id="modalTaskUser" class="text-gray-900 dark:text-gray-100">John Doe</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date d'échéance</label>
                    <p id="modalTaskDate" class="text-gray-900 dark:text-gray-100">25/12/2024</p>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex gap-3 justify-end">
                <div class="flex gap-2">
                    <button id="editTaskBtn" 
                            class="px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        <i class="fas fa-edit mr-2"></i>Modifier
                    </button>
                    <button id="deleteTaskBtn" 
                            class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                        <i class="fas fa-trash mr-2"></i>Supprimer
                    </button>
                </div>
                <button id="closeTaskDetailModal" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- FullCalendar JS --}}
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/fr.global.min.js"></script>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const calendarEl = document.getElementById('calendar');
                
                // Données des tâches depuis le serveur
                const tasks = @json($calendarTasks);
                
                // Configuration des couleurs par statut
                const statusColors = {
                    'Backlog': '#6b7280',
                    'To Do': '#3b82f6',
                    'In Progress': '#f59e0b',
                    'To Be Checked': '#8b5cf6',
                    'Done': '#10b981'
                };
                
                // Préparer les événements pour le calendrier
                const events = tasks.map(task => ({
                    id: task.id,
                    title: task.title,
                    start: task.start,
                    backgroundColor: statusColors[task.extendedProps.column] || task.backgroundColor,
                    borderColor: statusColors[task.extendedProps.column] || task.borderColor,
                    textColor: '#ffffff',
                    className: `status-${task.extendedProps.column.toLowerCase().replace(/\s+/g, '')}`,
                    extendedProps: task.extendedProps
                }));
                
                // Initialiser le calendrier
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'fr',
                    height: 'auto',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    buttonText: {
                        today: 'Aujourd\'hui',
                        month: 'Mois',
                        week: 'Semaine',
                        day: 'Jour'
                    },
                    events: events,
                    eventDisplay: 'block',
                    dayMaxEventRows: 3,
                    moreLinkClick: 'popover',
                    eventClick: function(info) {
                        showTaskDetail(info.event);
                    },
                    dateClick: function(info) {
                        console.log('Date cliquée:', info.dateStr);
                    },
                    eventMouseEnter: function(info) {
                        info.el.style.transform = 'scale(1.05)';
                        info.el.style.zIndex = '10';
                    },
                    eventMouseLeave: function(info) {
                        info.el.style.transform = 'scale(1)';
                        info.el.style.zIndex = '1';
                    }
                });
                
                calendar.render();
                
                // Gestionnaires pour les boutons de vue
                document.getElementById('dayViewBtn').addEventListener('click', function() {
                    calendar.changeView('timeGridDay');
                    updateViewButtons('day');
                });
                
                document.getElementById('weekViewBtn').addEventListener('click', function() {
                    calendar.changeView('timeGridWeek');
                    updateViewButtons('week');
                });
                
                document.getElementById('monthViewBtn').addEventListener('click', function() {
                    calendar.changeView('dayGridMonth');
                    updateViewButtons('month');
                });
                
                function updateViewButtons(activeView) {
                    const buttons = ['dayViewBtn', 'weekViewBtn', 'monthViewBtn'];
                    const views = ['day', 'week', 'month'];
                    
                    buttons.forEach((buttonId, index) => {
                        const button = document.getElementById(buttonId);
                        if (views[index] === activeView) {
                            button.className = 'px-3 py-2 text-sm bg-blue-600 text-white rounded-lg';
                        } else {
                            button.className = 'px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors';
                        }
                    });
                }
                
                // Affichage du détail d'une tâche
                function showTaskDetail(event) {
                    const modal = document.getElementById('taskDetailModal');
                    
                    document.getElementById('modalTaskTitle').textContent = event.title;
                    
                    const statusElement = document.getElementById('modalTaskStatus');
                    statusElement.textContent = event.extendedProps.column;
                    statusElement.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusClass(event.extendedProps.column)}`;
                    
                    // Catégorie (si existe)
                    const categoryContainer = document.getElementById('modalTaskCategoryContainer');
                    const categoryElement = document.getElementById('modalTaskCategory');
                    if (event.extendedProps.category) {
                        categoryContainer.classList.remove('hidden');
                        categoryElement.textContent = event.extendedProps.category;
                        categoryElement.style.backgroundColor = event.backgroundColor;
                    } else {
                        categoryContainer.classList.add('hidden');
                    }
                    
                    document.getElementById('modalTaskUser').textContent = event.extendedProps.user;
                    document.getElementById('modalTaskDate').textContent = new Date(event.start).toLocaleDateString('fr-FR');
                    
                    // Configurer les boutons d'action
                    document.getElementById('editTaskBtn').onclick = () => editTaskFromCalendar(event.id);
                    document.getElementById('deleteTaskBtn').onclick = () => deleteTaskFromCalendar(event.id);
                    
                    modal.classList.remove('hidden');
                }
                
                function getStatusClass(status) {
                    const classes = {
                        'Backlog': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                        'To Do': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                        'In Progress': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                        'To Be Checked': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                        'Done': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                    };
                    return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                }
                
                // Fonctions d'action
                function editTaskFromCalendar(taskId) {
                    // Rediriger vers la vue Kanban avec l'édition de la tâche
                    window.location.href = `{{ route('projects.show', $project) }}?edit=${taskId}`;
                }
                
                function deleteTaskFromCalendar(taskId) {
                    if (confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')) {
                        fetch(`/tasks/${taskId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        }).then(response => {
                            if (response.ok) {
                                window.location.reload();
                            } else {
                                alert('Erreur lors de la suppression');
                            }
                        });
                    }
                }
                
                // Fermer le modal
                document.getElementById('closeTaskDetailModal').addEventListener('click', function() {
                    document.getElementById('taskDetailModal').classList.add('hidden');
                });
                
                // Fermer le modal en cliquant en dehors
                document.getElementById('taskDetailModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.classList.add('hidden');
                    }
                });
                
                // Fermer avec Escape
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        document.getElementById('taskDetailModal').classList.add('hidden');
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
