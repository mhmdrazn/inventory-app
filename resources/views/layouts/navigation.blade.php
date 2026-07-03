<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if(auth()->user()->hasRole('admin', 'staff'))
                        <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                            {{ __('Products') }}
                        </x-nav-link>
                    @endif

                    @if(auth()->user()->hasRole('admin'))
                        <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                            {{ __('Categories') }}
                        </x-nav-link>
                    @endif

                    @if(auth()->user()->hasRole('admin', 'staff'))
                        <x-nav-link :href="route('borrowings.index')" :active="request()->routeIs('borrowings.*')">
                            {{ __('Borrowings') }}
                        </x-nav-link>
                    @endif

                    @if(auth()->user()->hasRole('admin', 'manager'))
                        <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                            {{ __('Reports') }}
                        </x-nav-link>
                    @endif

                    @if(auth()->user()->hasRole('admin'))
                        <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                            {{ __('User Management') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">
                <button
                    type="button"
                    x-data="{
                        dark: document.documentElement.classList.contains('dark'),
                        toggle() {
                            this.dark = !this.dark;
                            document.documentElement.classList.toggle('dark', this.dark);
                            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
                        }
                    }"
                    @click="toggle()"
                    :aria-label="dark ? 'Switch to light mode' : 'Switch to dark mode'"
                    class="inline-flex items-center justify-center h-9 w-9 rounded-md border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                >
                    <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1.5m0 15V21m8.485-8.485H21M3 12h1.515m14.02-6.02l-1.06 1.06M6.525 17.475l-1.06 1.06m0-13.06l1.06 1.06m10.95 10.95l1.06 1.06M12 7.5A4.5 4.5 0 1 1 12 16.5a4.5 4.5 0 0 1 0-9z" />
                    </svg>
                    <svg x-show="dark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
                    </svg>
                </button>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if(auth()->user()->hasRole('admin', 'staff'))
                <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                    {{ __('Products') }}
                </x-responsive-nav-link>
            @endif

            @if(auth()->user()->hasRole('admin'))
                <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                    {{ __('Categories') }}
                </x-responsive-nav-link>
            @endif

            @if(auth()->user()->hasRole('admin', 'staff'))
                <x-responsive-nav-link :href="route('borrowings.index')" :active="request()->routeIs('borrowings.*')">
                    {{ __('Borrowings') }}
                </x-responsive-nav-link>
            @endif

            @if(auth()->user()->hasRole('admin', 'manager'))
                <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                    {{ __('Reports') }}
                </x-responsive-nav-link>
            @endif

            @if(auth()->user()->hasRole('admin'))
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                    {{ __('User Management') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <button
                    type="button"
                    x-data="{
                        dark: document.documentElement.classList.contains('dark'),
                        toggle() {
                            this.dark = !this.dark;
                            document.documentElement.classList.toggle('dark', this.dark);
                            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
                        }
                    }"
                    @click="toggle()"
                    class="w-full text-left flex items-center gap-2 pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out"
                >
                    <span x-show="!dark">🌙 Dark Mode</span>
                    <span x-show="dark" x-cloak>☀️ Light Mode</span>
                </button>
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
