<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $project->name }} - Calendrier
            </h2>
            <nav class="flex space-x-4">
                <a href="{{ route('projects.show', $project) }}" 
                   class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('projects.show') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    Tableau Kanban
                </a>
                <a href="{{ route('projects.calendar', $project) }}" 
                   class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('projects.calendar') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    Calendrier
                </a>
                <a href="{{ route('project.members.index', $project) }}" 
                   class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('project.members.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    Membres
                </a>
            </nav>
        </div>
    </x-slot>

    @vite(['resources/css/calendar.css'])

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Contrôles du calendrier -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <button id="prevBtn" class="p-2 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <h3 id="currentDate" class="text-lg font-semibold">{{ now()->format('F Y') }}</h3>
                        <button id="nextBtn" class="p-2 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                        <button id="todayBtn" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Aujourd'hui
                        </button>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <button id="dayView" class="px-3 py-2 text-sm rounded-lg bg-blue-100 text-blue-700 font-medium">
                            Jour
                        </button>
                        <button id="weekView" class="px-3 py-2 text-sm rounded-lg hover:bg-gray-100">
                            Semaine
                        </button>
                        <button id="monthView" class="px-3 py-2 text-sm rounded-lg hover:bg-gray-100">
                            Mois
                        </button>
                    </div>
                </div>
            </div>

            <!-- Calendrier -->
            <div class="bg-white rounded-lg shadow-sm">
                <div id="calendarContainer" class="p-6">
                    <!-- Le contenu du calendrier sera généré par JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour créer/éditer une tâche -->
    <div id="taskModal" class="modal hidden">
        <div class="modal-content">
            <h2 id="taskModalTitle">Nouvelle tâche</h2>
            <form id="taskForm">
                <div class="form-group">
                    <label for="taskTitle">Titre de la tâche</label>
                    <input type="text" id="taskTitle" name="taskTitle" required>
                </div>
                <div class="form-group">
                    <label for="taskCategory">Catégorie</label>
                    <input type="text" id="taskCategory" name="taskCategory" required>
                </div>
                <div class="form-group">
                    <label for="taskColor">Couleur</label>
                    <input type="color" id="taskColor" name="taskColor" value="#3B82F6">
                </div>
                <div class="form-group">
                    <label for="taskDate">Date</label>
                    <input type="date" id="taskDate" name="taskDate" required>
                </div>
                <div class="form-group">
                    <label for="taskColumn">Colonne</label>
                    <select id="taskColumn" name="taskColumn" required>
                        <option value="Backlog">Backlog</option>
                        <option value="To Do">To Do</option>
                        <option value="In Progress">In Progress</option>
                        <option value="To Be Checked">To Be Checked</option>
                        <option value="Done">Done</option>
                    </select>
                </div>
                <input type="hidden" id="taskMode" name="taskMode" value="create">
                <input type="hidden" id="editTargetId">
                <div class="modal-buttons">
                    <button type="button" id="cancelTaskModal" class="cancel">Annuler</button>
                    <button type="submit" class="submit">Valider</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const projectId = {{ $project->id }};
        const tasks = @json($tasks);
        const userInitials = @json($userInitials);
    </script>
    @vite(['resources/js/calendar.js'])
</x-app-layout> 