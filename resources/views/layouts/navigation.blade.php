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
                            {{-- <x-dropdown-link :href="route('admin.dashboard')"> --}}
                            <x-dropdown-link :href="route('admin.dashboard')">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                {{ __('Admin Dashboard') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('dashboard')">
                                <i class="fas fa-eye mr-2"></i>
                                {{ __('Switch to User View') }}
                            </x-dropdown-link>
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
                <x-responsive-nav-link :href="route('admin.users.index')" :active="false">
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
