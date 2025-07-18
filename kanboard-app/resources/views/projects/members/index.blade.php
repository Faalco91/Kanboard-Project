<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Membres du projet :') }} {{ $project->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Formulaire d'invitation -->
            <div class="p-6 mb-6 bg-white rounded-lg shadow-md">
                <h3 class="mb-4 text-lg font-semibold">{{ __('Inviter un membre') }}</h3>
                <form action="{{ route('project.members.invite', $project) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="email" :value="__('Adresse email')" />
                        <x-text-input id="email" type="email" name="email" class="block w-full mt-1" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="role" :value="__('Rôle')" />
                        <select id="role" name="role" class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="member">{{ __('Membre') }}</option>
                            <option value="admin">{{ __('Administrateur') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <x-primary-button>
                        {{ __('Envoyer l\'invitation') }}
                    </x-primary-button>
                </form>
            </div>

            <!-- Liste des membres -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6">
                    <h3 class="mb-4 text-lg font-semibold">{{ __('Membres actuels') }}</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Nom') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Email') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Rôle') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Statut') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($project->members as $member)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $member->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $member->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $member->pivot->role === 'owner' ? 'bg-purple-100 text-purple-800' : 
                                                   ($member->pivot->role === 'admin' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                                {{ ucfirst($member->pivot->role) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $member->pivot->status === 'accepted' ? 'bg-green-100 text-green-800' : 
                                                   ($member->pivot->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ ucfirst($member->pivot->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if($member->pivot->role !== 'owner')
                                                <form action="{{ route('project.members.remove', [$project, $member]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                                            onclick="return confirm('{{ __('Êtes-vous sûr de vouloir retirer ce membre ?') }}')">
                                                        {{ __('Retirer') }}
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 