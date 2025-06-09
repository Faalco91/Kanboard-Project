<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Invitation au projet
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Invitation au projet "{{ $project->name }}"</h3>
                
                <p class="mb-4 text-gray-700">Vous avez été invité(e) à rejoindre ce projet en tant que {{ $member->role }}.</p>

                <div class="flex space-x-4">
                    <form method="POST" action="{{ route('project.members.accept-invitation', $project) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Accepter l'invitation
                        </button>
                    </form>

                    <form method="POST" action="{{ route('project.members.reject-invitation', $project) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Refuser l'invitation
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 