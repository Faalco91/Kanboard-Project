<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold leading-tight text-gray-800 dark:text-gray-200">
                    {{ __('Statistiques') }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Analysez votre productivité et suivez vos projets
                </p>
            </div>
            
            {{-- Bouton de retour au dashboard --}}
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 w-full sm:w-auto justify-center focus-ring">
                <i class="fas fa-arrow-left"></i>
                {{ __('Retour au Dashboard') }}
            </a>
        </div>
    </x-slot>

    @push('styles')
        <style>
            .stats-card {
                @apply bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700;
                @apply p-6 transition-all duration-200 hover:shadow-md hover:scale-105;
            }
            
            .gradient-bg {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
            
            .fade-in {
                animation: fadeIn 0.6s ease-in-out;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            .progress-bar {
                @apply w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2;
            }
            
            .progress-fill {
                @apply h-2 rounded-full transition-all duration-500 ease-out;
            }
            
            .loading-spinner {
                @apply animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600;
            }
        </style>
    @endpush

    <div class="fade-in">
        @livewire('statistics.statistics-dashboard')
    </div>
</x-app-layout> 