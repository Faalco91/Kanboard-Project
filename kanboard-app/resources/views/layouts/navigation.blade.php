<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left side -->
            <div class="flex">
                <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('home') }}" class="flex items-center space-x-2 group">
                            <img src="{{ asset('images/Kanboard_logo.svg') }}" 
                                 alt="Logo Kanboard" 
                                 class="w-12 h-12 group-hover:scale-105 transition-transform">
                            <span class="font-bold text-xl text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                Kanboard
                            </span>
                        </a>
                    </div>

                <!-- Navigation Links Desktop -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="focus-ring">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.*')" class="focus-ring">
                        <i class="fas fa-folder mr-2"></i>
                        {{ __('Projets') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Right side -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">
                
                {{-- Bouton de création rapide --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-plus mr-1"></i>
                        <i class="fas fa-chevron-down ml-1 text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
                    </button>
                    
                    <!-- Dropdown -->
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 z-50 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
                        <div class="py-1">
                            <a href="{{ route('projects.create') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-folder-plus mr-2 text-blue-500"></i>
                                Nouveau projet
                            </a>
                        </div>
                    </div>
                </div>
                
                {{-- Toggle de thème --}}
                <div x-data="themeToggle()" class="flex items-center">
                    <button @click="toggle()" 
                            class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                            title="Basculer le thème">
                        <i class="fas fa-sun text-lg" x-show="!isDark"></i>
                        <i class="fas fa-moon text-lg" x-show="isDark"></i>
                    </button>
                </div>

                <!-- User Menu -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                        
                        {{-- Avatar utilisateur --}}
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-2">
                            <span class="text-white text-sm font-semibold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        </div>
                        
                        <span class="hidden md:block">{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down ml-2 text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
                    </button>
                    
                    <!-- Dropdown User Menu -->
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 z-50 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
                        
                        {{-- Informations utilisateur --}}
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ Auth::user()->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                        </div>
                        
                        {{-- Menu items --}}
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-user mr-3 text-gray-400"></i>
                                Profil
                            </a>
                            
                            <a href="{{ route('projects.index') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-folder mr-3 text-gray-400"></i>
                                Mes projets
                            </a>
                            
                            <div class="border-t border-gray-200 dark:border-gray-700"></div>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-sign-out-alt mr-3 text-gray-400"></i>
                                    {{ __('Déconnexion') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="-me-2 flex items-center sm:hidden space-x-2">
                {{-- Toggle de thème mobile --}}
                <div x-data="themeToggle()">
                    <button @click="toggle()" 
                            class="p-2 rounded-lg text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-sun text-lg" x-show="!isDark"></i>
                        <i class="fas fa-moon text-lg" x-show="isDark"></i>
                    </button>
                </div>
                
                {{-- Menu hamburger --}}
                <button @click="open = !open" 
                        class="inline-flex items-center justify-center p-2 rounded-lg text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-bars text-lg" x-show="!open"></i>
                    <i class="fas fa-times text-lg" x-show="open"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="sm:hidden bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
        
        {{-- Navigation mobile --}}
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <i class="fas fa-tachometer-alt mr-2"></i>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.*')">
                <i class="fas fa-folder mr-2"></i>
                {{ __('Projets') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('projects.create')">
                <i class="fas fa-plus mr-2"></i>
                {{ __('Nouveau projet') }}
            </x-responsive-nav-link>
        </div>

        {{-- Utilisateur mobile --}}
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-700">
            <div class="px-4 pb-2">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-3">
                        <span class="text-white font-semibold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                    </div>
                </div>
            </div>

            <div class="space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    <i class="fas fa-user mr-2"></i>
                    {{ __('Profil') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        {{ __('Déconnexion') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
