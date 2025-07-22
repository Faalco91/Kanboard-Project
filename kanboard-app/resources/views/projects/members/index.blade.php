<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold leading-tight text-gray-800 dark:text-gray-200">
                    Membres du projet
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Gestion des membres de {{ $project->name }}
                </p>
            </div>
            
            <div class="flex gap-3">
                <a href="{{ route('projects.show', $project) }}" 
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors">
                    <i class="fas fa-columns mr-1"></i>
                    Vue Kanban
                </a>
                <a href="{{ route('projects.edit', $project) }}" 
                   class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    Retour aux paramètres
                </a>
            </div>
        </div>
    </x-slot>

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

            @if(session('error'))
                <div class="mb-6 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <div class="flex">
                        <i class="fas fa-exclamation-circle text-red-400 mr-3 mt-0.5"></i>
                        <p class="text-red-800 dark:text-red-200">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            {{-- Formulaire d'invitation --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        <i class="fas fa-user-plus mr-2 text-blue-600 dark:text-blue-400"></i>
                        Inviter un membre
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Ajoutez un nouveau membre à votre équipe projet
                    </p>
                </div>
                
                <form action="{{ route('project.members.invite', $project) }}" method="POST" class="p-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Adresse email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100 transition-colors"
                                   placeholder="exemple@email.com">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Rôle
                            </label>
                            <select id="role" 
                                    name="role" 
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100 transition-colors">
                                <option value="member">Membre</option>
                                <option value="admin">Administrateur</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button type="submit" 
                                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-paper-plane"></i>
                            Envoyer l'invitation
                        </button>
                    </div>
                </form>
            </div>

            {{-- Liste des membres --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            <i class="fas fa-users mr-2 text-green-600 dark:text-green-400"></i>
            Membres actuels
        </h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            {{ $project->members->count() + 1 }} membres dans ce projet (incluant le propriétaire)
        </p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        <i class="fas fa-user mr-2"></i>Membre
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        <i class="fas fa-envelope mr-2"></i>Email
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        <i class="fas fa-shield-alt mr-2"></i>Rôle
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        <i class="fas fa-info-circle mr-2"></i>Statut
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        <i class="fas fa-cogs mr-2"></i>Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                {{-- Propriétaire du projet --}}
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/50 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-crown text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $project->user->name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Créateur du projet
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $project->user->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-200">
                            <i class="fas fa-crown mr-1"></i>Propriétaire
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200">
                            <i class="fas fa-check mr-1"></i>Actif
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-xs text-gray-500 dark:text-gray-400 italic">
                            Propriétaire
                        </span>
                    </td>
                </tr>

                {{-- Membres du projet --}}
                @forelse($project->members->where('id', '!=', $project->user_id) as $member)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-gray-600 dark:text-gray-400"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $member->name }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Membre depuis {{ $member->pivot->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $member->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $member->pivot->role === 'admin' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200' }}">
                                <i class="fas {{ $member->pivot->role === 'admin' ? 'fa-user-shield' : 'fa-user' }} mr-1"></i>
                                {{ $member->pivot->role === 'admin' ? 'Administrateur' : 'Membre' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $member->pivot->status === 'accepted' ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200' : 
                                   ($member->pivot->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-200' : 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200') }}">
                                <i class="fas {{ $member->pivot->status === 'accepted' ? 'fa-check' : ($member->pivot->status === 'pending' ? 'fa-clock' : 'fa-times') }} mr-1"></i>
                                {{ $member->pivot->status === 'accepted' ? 'Actif' : ($member->pivot->status === 'pending' ? 'En attente' : 'Rejeté') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($project->user_id === auth()->id())
                                <form action="{{ route('project.members.remove', [$project, $member]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium transition-colors"
                                            onclick="return confirm('Êtes-vous sûr de vouloir retirer {{ $member->name }} du projet ?')">
                                        <i class="fas fa-user-minus"></i>
                                        Retirer
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400 dark:text-gray-500 italic">
                                    Non autorisé
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-user-plus text-gray-400 text-3xl mb-4"></i>
                                <p class="text-gray-500 dark:text-gray-400 text-sm">
                                    Aucun membre supplémentaire dans ce projet
                                </p>
                                <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">
                                    Invitez des collaborateurs pour commencer à travailler en équipe
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

            {{-- Informations supplémentaires --}}
            <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6">
                <h4 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-3">
                    <i class="fas fa-info-circle mr-2"></i>
                    Gestion des membres
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-800 dark:text-blue-200">
                    <div>
                        <h5 class="font-medium mb-2">Rôles disponibles :</h5>
                        <ul class="space-y-1">
                            <li><strong>Propriétaire :</strong> Contrôle total du projet</li>
                            <li><strong>Administrateur :</strong> Gestion des tâches et membres</li>
                            <li><strong>Membre :</strong> Participation aux tâches</li>
                        </ul>
                    </div>
                    <div>
                        <h5 class="font-medium mb-2">Statuts des invitations :</h5>
                        <ul class="space-y-1">
                            <li><strong>En attente :</strong> Invitation envoyée</li>
                            <li><strong>Actif :</strong> Membre confirmé</li>
                            <li><strong>Rejeté :</strong> Invitation refusée</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Amélioration des transitions */
            tr:hover {
                transform: scale(1.01);
                transition: transform 0.2s ease;
            }
            
            /* Amélioration des badges */
            .rounded-full {
                transition: all 0.2s ease;
            }
            
            /* Amélioration du contraste dark mode */
            .dark input:focus,
            .dark select:focus {
                background-color: #374151 !important;
                border-color: #3b82f6 !important;
                color: white !important;
            }
            
            .dark input::placeholder,
            .dark select::placeholder {
                color: #9ca3af !important;
            }
        </style>
    @endpush
</x-app-layout>
