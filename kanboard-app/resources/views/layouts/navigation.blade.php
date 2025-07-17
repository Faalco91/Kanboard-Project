<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left side -->
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <i class="fas fa-columns text-2xl text-blue-600 dark:text-blue-400"></i>
                        <span class="font-bold text-xl text-gray-900 dark:text-gray-100">Kanboard</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Right side -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">
                
                {{-- Toggle de thème --}}
                <div x-data="themeToggle()">
                    <label class="theme-toggle">
                        <input type="checkbox" x-model="isDark" @change="toggle()" class="sr-only">
                        <span class="theme-slider">
                            <i class="fas fa-sun absolute left-1 top-1 text-xs text-yellow-500 transition-opacity" 
                               :class="{ 'opacity-0': isDark, 'opacity-100': !isDark }"></i>
                            <i class="fas fa-moon absolute right-1 top-1 text-xs text-blue-400 transition-opacity" 
                               :class="{ 'opacity-100': isDark, 'opacity-0': !isDark }"></i>
                        </span>
                    </label>
                </div>

                <!-- User Menu -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition">
                        
                        {{-- Avatar utilisateur --}}
                        <div class="user-avatar size-sm mr-2">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        
                        <span>{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down ml-1 text-xs transition-transform" 
                           :class="{ 'rotate-180': open }"></i>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="dropdown-enter"
                         x-transition:enter-start="dropdown-enter-from"
                         x-transition:enter-end="dropdown-enter-to"
                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg z-50">
                        
                        <a href="{{ route('profile.edit') }}" 
                           class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fas fa-user mr-3"></i>
                            {{ __('Profil') }}
                        </a>
                        
                        <div class="border-t border-gray-100 dark:border-gray-700"></div>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full text-left flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-sign-out-alt mr-3"></i>
                                {{ __('Déconnexion') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open" 
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none transition">
                    <i class="fas fa-bars text-lg" x-show="!open"></i>
                    <i class="fas fa-times text-lg" x-show="open"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div x-show="open" class="sm:hidden bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <i class="fas fa-tachometer-alt mr-2"></i>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.*')">
                <i class="fas fa-folder mr-2"></i>
                {{ __('Projets') }}
            </x-responsive-nav-link>
        </div>

        <!-- Mobile User Info -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
            <div class="px-4 flex items-center space-x-3">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div>
                    <div class="font-medium text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                </div>
            </div>
            
            <!-- Theme toggle mobile -->
            <div class="px-4 py-3" x-data="themeToggle()">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Mode sombre</span>
                    <label class="theme-toggle">
                        <input type="checkbox" x-model="isDark" @change="toggle()" class="sr-only">
                        <span class="theme-slider"></span>
                    </label>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    <i class="fas fa-user mr-2"></i>
                    {{ __('Profil') }}
                </x-responsive-nav-link>

                {{-- Bouton de déconnexion corrigé --}}
                <form method="POST" action="{{ route('logout') }}" class="block">
                    @csrf
                    <x-responsive-nav-link 
                        :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();"
                        class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        {{ __('Déconnexion') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

{{-- Script pour le thème toggle --}}
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('themeToggle', () => ({
        isDark: localStorage.getItem('kanboard-theme') === 'dark',
        
        toggle() {
            this.isDark = !this.isDark;
            const theme = this.isDark ? 'dark' : 'light';
            localStorage.setItem('kanboard-theme', theme);
            
            if (this.isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    }));
});
</script>
