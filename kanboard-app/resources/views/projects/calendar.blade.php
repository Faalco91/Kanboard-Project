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
            /* Personnalisation du calendrier pour le dark mode */
            .fc {
                background: white;
                border-radius: 0.75rem;
                overflow: hidden;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            }
            
            .dark .fc {
                background: rgb(31 41 55);
                color: rgb(243 244 246);
            }
            
            .fc-toolbar {
                background: rgb(249 250 251);
                padding: 1rem;
                border-bottom: 1px solid rgb(229 231 235);
            }
            
            .dark .fc-toolbar {
                background: rgb(17 24 39);
                border-bottom-color: rgb(55 65 81);
            }
            
            .fc-button {
                background: rgb(59 130 246) !important;
                border-color: rgb(59 130 246) !important;
                border-radius: 0.5rem !important;
                font-weight: 500 !important;
                padding: 0.5rem 1rem !important;
            }
            
            .fc-button:hover {
                background: rgb(37 99 235) !important;
                border-color: rgb(37 99 235) !important;
            }
            
            .fc-button-primary:disabled {
                background: rgb(156 163 175) !important;
                border-color: rgb(156 163 175) !important;
            }
            
            .fc-daygrid-day {
                border-color: rgb(229 231 235);
            }
            
            .dark .fc-daygrid-day {
                border-color: rgb(55 65 81);
            }
            
            .fc-day-today {
                background: rgb(239 246 255) !important;
            }
            
            .dark .fc-day-today {
                background: rgb(30 58 138) !important;
            }
            
            .fc-event {
                border-radius: 0.375rem;
                padding: 0.125rem 0.375rem;
                font-weight: 500;
                font-size: 0.75rem;
                cursor: pointer;
            }
            
            .fc-event:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }
            
            .calendar-legend {
                display: flex;
                flex-wrap: wrap;
                gap: 1rem;
                margin-bottom: 1rem;
            }
            
            .legend-item {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                font-size: 0.875rem;
            }
            
            .legend-color {
                width: 1rem;
                height: 1rem;
                border-radius: 0.25rem;
            }
        </style>
    @endpush

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Légende --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Légende</h3>
                <div class="calendar-legend">
                    <div class="legend-item">
                        <div class="legend-color bg-gray-500"></div>
                        <span class="text-gray-700 dark:text-gray-300">Backlog</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color bg-blue-500"></div>
                        <span class="text-gray-700 dark:text-gray-300">To Do</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color bg-yellow-500"></div>
                        <span class="text-gray-700 dark:text-gray-300">In Progress</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color bg-purple-500"></div>
                        <span class="text-gray-700 dark:text-gray-300">To Be Checked</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color bg-green-500"></div>
                        <span class="text-gray-700 dark:text-gray-300">Done</span>
                    </div>
                </div>
            </div>

            {{-- Calendrier --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    {{-- Modal de détails de tâche --}}
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
                    <span id="modalTaskStatus" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"></span>
                </div>
                
                <div id="modalTaskCategoryContainer" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catégorie</label>
                    <span id="modalTaskCategory" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assigné à</label>
                    <p id="modalTaskUser" class="text-sm text-gray-900 dark:text-gray-100"></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date d'échéance</label>
                    <p id="modalTaskDate" class="text-sm text-gray-900 dark:text-gray-100"></p>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-between">
                <div class="flex space-x-2">
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
                    extendedProps: task.extendedProps
                }));
                
                // Initialiser le calendrier
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'fr',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    height: 'auto',
                    events: events,
                    eventDisplay: 'block',
                    dayMaxEvents: 3,
                    moreLinkClick: 'popover',
                    
                    // Gestion du clic sur un événement
                    eventClick: function(info) {
                        showTaskDetail(info.event);
                    },
                    
                    // Styles personnalisés
                    eventDidMount: function(info) {
                        // Ajouter des classes CSS personnalisées
                        info.el.classList.add('transition-all', 'duration-200');
                        
                        // Tooltip
                        info.el.title = `${info.event.title}\nStatut: ${info.event.extendedProps.column}\nAssigné à: ${info.event.extendedProps.user}`;
                    },
                    
                    // Responsive
                    windowResizeDelay: 100
                });
                
                calendar.render();
                
                // Fonction pour afficher les détails d'une tâche
                function showTaskDetail(event) {
                    const modal = document.getElementById('taskDetailModal');
                    const statusColors = {
                        'Backlog': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                        'To Do': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                        'In Progress': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                        'To Be Checked': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                        'Done': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                    };
                    
                    // Remplir le modal
                    document.getElementById('modalTaskTitle').textContent = event.title;
                    
                    const statusElement = document.getElementById('modalTaskStatus');
                    statusElement.textContent = event.extendedProps.column;
                    statusElement.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColors[event.extendedProps.column] || 'bg-gray-100 text-gray-800'}`;
                    
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
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Supprimer l'événement du calendrier
                                const eventToRemove = calendar.getEventById(taskId);
                                if (eventToRemove) {
                                    eventToRemove.remove();
                                }
                                document.getElementById('taskDetailModal').classList.add('hidden');
                                
                                // Notification de succès
                                if (typeof window.showNotification === 'function') {
                                    window.showNotification('Tâche supprimée avec succès', 'success');
                                }
                            } else {
                                alert('Erreur lors de la suppression');
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert('Erreur lors de la suppression');
                        });
                    }
                }
                
                // Fermeture du modal
                document.getElementById('closeTaskDetailModal').addEventListener('click', () => {
                    document.getElementById('taskDetailModal').classList.add('hidden');
                });
                
                // Fermer le modal en cliquant en dehors
                document.getElementById('taskDetailModal').addEventListener('click', (e) => {
                    if (e.target === document.getElementById('taskDetailModal')) {
                        document.getElementById('taskDetailModal').classList.add('hidden');
                    }
                });
                
                // Adaptation du thème
                function updateCalendarTheme() {
                    const isDark = document.documentElement.classList.contains('dark');
                    if (isDark) {
                        calendarEl.classList.add('dark-theme');
                    } else {
                        calendarEl.classList.remove('dark-theme');
                    }
                }
                
                // Écouter les changements de thème
                const observer = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                            updateCalendarTheme();
                        }
                    });
                });
                
                observer.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['class']
                });
                
                // Appliquer le thème initial
                updateCalendarTheme();
                
                // Responsive: adapter la vue sur mobile
                function handleResize() {
                    if (window.innerWidth < 768) {
                        calendar.changeView('timeGridDay');
                    } else {
                        calendar.changeView('dayGridMonth');
                    }
                }
                
                // Écouter les changements de taille d'écran
                window.addEventListener('resize', handleResize);
                
                // Vérifier la taille initiale
                handleResize();
            });
        </script>
    @endpush
</x-app-layout>
