@if(session('success'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="flex items-center p-4 mb-4 text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 dark:border-green-800" 
             role="alert"
             x-data="{ show: true }" 
             x-show="show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100">
            <i class="fas fa-check-circle mr-3"></i>
            <span class="font-medium">{{ session('success') }}</span>
            <button type="button" 
                    class="ml-auto text-green-500 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300"
                    @click="show = false">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="flex items-center p-4 mb-4 text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" 
             role="alert"
             x-data="{ show: true }" 
             x-show="show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100">
            <i class="fas fa-exclamation-circle mr-3"></i>
            <span class="font-medium">{{ session('error') }}</span>
            <button type="button" 
                    class="ml-auto text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                    @click="show = false">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif

@if(session('warning'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="flex items-center p-4 mb-4 text-yellow-800 border border-yellow-300 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300 dark:border-yellow-600" 
             role="alert"
             x-data="{ show: true }" 
             x-show="show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100">
            <i class="fas fa-exclamation-triangle mr-3"></i>
            <span class="font-medium">{{ session('warning') }}</span>
            <button type="button" 
                    class="ml-auto text-yellow-500 hover:text-yellow-700 dark:text-yellow-300 dark:hover:text-yellow-100"
                    @click="show = false">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif
