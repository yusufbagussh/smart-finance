{{-- resources/views/layouts/navigation.blade.php --}}
{{-- <nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <span class="text-xl font-bold text-gray-800 dark:text-gray-200">💰 FinanceTracker</span>
                    </a>
                </div>

                <!-- Navigation Links (Hidden on mobile, shown on desktop) -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <i class="fas fa-home mr-2"></i>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                        <i class="fas fa-exchange-alt mr-2"></i>
                        {{ __('Transactions') }}
                    </x-nav-link>
                    <x-nav-link :href="route('budgets.index')" :active="request()->routeIs('budgets.*')">
                        <i class="fas fa-chart-pie mr-2"></i>
                        {{ __('Budget') }}
                    </x-nav-link>
                    <x-nav-link :href="route('ml.index')" :active="request()->routeIs('ml.*')">
                        <i class="fas fa-brain mr-2"></i>
                        {{ __('AI Features') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            <i class="fas fa-user mr-2"></i>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (Mobile) -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <i class="fas fa-home mr-2"></i>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                <i class="fas fa-exchange-alt mr-2"></i>
                {{ __('Transactions') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('budgets.index')" :active="request()->routeIs('budgets.*')">
                <i class="fas fa-chart-pie mr-2"></i>
                {{ __('Budget') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('ml.index')" :active="request()->routeIs('ml.*')">
                <i class="fas fa-brain mr-2"></i>
                {{ __('AI Features') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    <i class="fas fa-user mr-2"></i>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav> --}}

{{-- resources/views/layouts/navigation.blade.php - UPDATE --}}
<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}">
                        <span class="text-xl font-bold text-gray-800 dark:text-gray-200">
                            💰 FinanceTracker
                            @if (auth()->user()->isAdmin())
                                <span
                                    class="ml-2 text-xs px-2 py-1 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 rounded-full">Admin</span>
                            @endif
                        </span>
                    </a>
                </div>

                <!-- Navigation Links (Hidden on mobile, shown on desktop) -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    @if (auth()->user()->isAdmin())
                        {{-- Cek route --}}
                        @if (request()->routeIs('admin.*'))
                            {{-- Admin Navigation --}}
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('dashboard')">
                                <i class="fas fa-home mr-2"></i>
                                {{ __('Dashboard') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">
                                <i class="fas fa-tags mr-2"></i>
                                {{ __('Categories') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                <i class="fas fa-users mr-2"></i>
                                {{ __('Users') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.monitoring.index')" :active="request()->routeIs('admin.monitoring.*')">
                                <i class="fas fa-heartbeat mr-2"></i>
                                {{ __('Monitoring') }}
                            </x-nav-link>
                            <x-nav-link :href="route('dashboard')" :active="false">
                                <i class="fas fa-eye mr-2"></i>
                                {{ __('User View') }}
                            </x-nav-link>
                        @else
                            {{-- User Navigation --}}
                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                                <i class="fas fa-home mr-2"></i>
                                {{ __('Dashboard') }}
                            </x-nav-link>
                            <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                                <i class="fas fa-exchange-alt mr-2"></i>
                                {{ __('Transactions') }}
                            </x-nav-link>
                            <x-nav-link :href="route('budgets.index')" :active="request()->routeIs('budgets.*')">
                                <i class="fas fa-chart-pie mr-2"></i>
                                {{ __('Budget') }}
                            </x-nav-link>
                            <x-nav-link :href="route('ml.index')" :active="request()->routeIs('ml.*')">
                                <i class="fas fa-brain mr-2"></i>
                                {{ __('AI Features') }}
                            </x-nav-link>
                            <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                                <i class="fas fa-file-download mr-2"></i>
                                {{ __('Reports') }}
                            </x-nav-link>
                        @endif
                        {{-- Admin Navigation --}}
                    @else
                        {{-- User Navigation --}}
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            <i class="fas fa-home mr-2"></i>
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                            <i class="fas fa-exchange-alt mr-2"></i>
                            {{ __('Transactions') }}
                        </x-nav-link>
                        <x-nav-link :href="route('budgets.index')" :active="request()->routeIs('budgets.*')">
                            <i class="fas fa-chart-pie mr-2"></i>
                            {{ __('Budget') }}
                        </x-nav-link>
                        <x-nav-link :href="route('ml.index')" :active="request()->routeIs('ml.*')">
                            <i class="fas fa-brain mr-2"></i>
                            {{ __('AI Features') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <!-- Add this button right here -->
                <button id="theme-toggle" type="button"
                    class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                            fill-rule="evenodd" clip-rule="evenodd"></path>
                    </svg>
                </button>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @if (auth()->user()->isAdmin())
                            <div class="px-4 py-2 text-xs text-gray-400 border-b border-gray-200 dark:border-gray-600">
                                <i class="fas fa-crown mr-1"></i>
                                Administrator
                            </div>
                            @if (request()->routeIs('admin.*'))
                                <x-dropdown-link :href="route('dashboard')">
                                    <i class="fas fa-eye mr-2"></i>
                                    {{ __('Switch to User View') }}
                                </x-dropdown-link>
                            @else
                                {{-- <x-dropdown-link :href="route('admin.dashboard')"> --}}
                                <x-dropdown-link :href="route('admin.dashboard')">
                                    <i class="fas fa-tachometer-alt mr-2"></i>
                                    {{ __('Switch to Admin View') }}
                                </x-dropdown-link>
                            @endif
                            <div class="border-t border-gray-200 dark:border-gray-600"></div>
                        @endif

                        <x-dropdown-link :href="route('profile.edit')">
                            <i class="fas fa-user mr-2"></i>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (Mobile) -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if (auth()->user()->isAdmin())
                {{-- Admin Mobile Navigation --}}
                @if (request()->routeIs('admin.*'))
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        {{ __('Admin Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                        <i class="fas fa-users mr-2"></i>
                        {{ __('Users') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">
                        <i class="fas fa-tags mr-2"></i>
                        {{ __('Categories') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.monitoring.index')" :active="request()->routeIs('admin.monitoring.*')">
                        <i class="fas fa-tags mr-2"></i>
                        {{ __('Monitoring') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('dashboard')" :active="false">
                        <i class="fas fa-eye mr-2"></i>
                        {{ __('User View') }}
                    </x-responsive-nav-link>
                @else
                    {{-- User Mobile Navigation --}}
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <i class="fas fa-home mr-2"></i>
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                        <i class="fas fa-exchange-alt mr-2"></i>
                        {{ __('Transactions') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('budgets.index')" :active="request()->routeIs('budgets.*')">
                        <i class="fas fa-chart-pie mr-2"></i>
                        {{ __('Budget') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('ml.index')" :active="request()->routeIs('ml.*')">
                        <i class="fas fa-brain mr-2"></i>
                        {{ __('AI Features') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                        <i class="fas fa-file-download mr-2"></i>
                        {{ __('Reports') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        <i class="fas fa-eye mr-2"></i>
                        {{ __('Admin View') }}
                    </x-responsive-nav-link>
                @endif
            @else
                {{-- User Mobile Navigation --}}
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    <i class="fas fa-home mr-2"></i>
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                    <i class="fas fa-exchange-alt mr-2"></i>
                    {{ __('Transactions') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('budgets.index')" :active="request()->routeIs('budgets.*')">
                    <i class="fas fa-chart-pie mr-2"></i>
                    {{ __('Budget') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('ml.index')" :active="request()->routeIs('ml.*')">
                    <i class="fas fa-brain mr-2"></i>
                    {{ __('AI Features') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                    <i class="fas fa-file-download mr-2"></i>
                    {{ __('Reports') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                    {{ Auth::user()->name }}
                    @if (auth()->user()->isAdmin())
                        <span
                            class="ml-2 text-xs px-2 py-1 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 rounded-full">Admin</span>
                    @endif
                </div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <div class="px-4 py-2 flex items-center justify-between">
                    <span class="font-medium text-base text-gray-800 dark:text-gray-200">Theme</span>
                    {{-- Gunakan ID unik tapi class yg sama untuk styling --}}
                    <button id="theme-toggle-mobile" type="button"
                        class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                        {{-- Salin SVG Dark Mode (Bulan) --}}
                        <svg id="theme-toggle-dark-icon-mobile" class="hidden w-5 h-5" fill="currentColor"
                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        {{-- Salin SVG Light Mode (Matahari) --}}
                        <svg id="theme-toggle-light-icon-mobile" class="hidden w-5 h-5" fill="currentColor"
                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                fill-rule="evenodd" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>

                <x-responsive-nav-link :href="route('profile.edit')">
                    <i class="fas fa-user mr-2"></i>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
