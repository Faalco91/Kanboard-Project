<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold">
                {{ __("Vos projets") }}
            </h1>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        
        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($projects->isEmpty())
                <div class="p-6 text-gray-900 bg-white shadow rounded">
                    {{ __("Vous n'avez pas de projets") }}
                </div>
            @else
                <ul class="space-y-2">
                    @foreach($projects as $project)
                        <li class="bg-white p-4 shadow rounded hover:bg-gray-50 transition">
                            <a href="{{ route('projects.show', $project->id) }}" class="text-blue-600 font-semibold">
                                {{ $project->name }}
                            </a>
                            <p class="text-sm text-gray-500">
                                {{ $project->description ?? 'Aucune description' }}
                            </p>
                        </li>
                    @endforeach
                </ul>
            @endif
    </div>
        </div>


                <button class="btn">
                    {{ __("Créer un projet") }}
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
