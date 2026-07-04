@php
    $user = auth()->user();
    $navSections = [
        [
            'label' => 'Menu',
            'items' => array_values(array_filter([
                [
                    'label' => 'Dashboard',
                    'route' => 'dashboard',
                    'active' => request()->routeIs('dashboard'),
                    'icon' => 'M2.25 12l8.954-8.955a1.5 1.5 0 0 1 2.121 0L22.28 12M4.5 9.75v10.125A1.125 1.125 0 0 0 5.625 21H9.75v-6h4.5v6h4.125A1.125 1.125 0 0 0 19.5 19.875V9.75',
                ],
                $user->hasRole('admin', 'staff') ? [
                    'label' => 'Barang',
                    'route' => 'products.index',
                    'active' => request()->routeIs('products.*'),
                    'icon' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z',
                ] : null,
                $user->hasRole('admin') ? [
                    'label' => 'Kategori',
                    'route' => 'categories.index',
                    'active' => request()->routeIs('categories.*'),
                    'icon' => 'M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 0 1-1.125-1.125v-3.75zM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-8.25zM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-2.25z',
                ] : null,
                $user->hasRole('admin', 'staff') ? [
                    'label' => 'Peminjaman',
                    'route' => 'borrowings.index',
                    'active' => request()->routeIs('borrowings.*'),
                    'icon' => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z',
                ] : null,
            ])),
        ],
        [
            'label' => 'Insight',
            'items' => array_values(array_filter([
                $user->hasRole('admin', 'manager') ? [
                    'label' => 'Laporan',
                    'route' => 'reports.index',
                    'active' => request()->routeIs('reports.*'),
                    'icon' => 'M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6',
                ] : null,
                $user->hasRole('admin') ? [
                    'label' => 'Pengguna',
                    'route' => 'users.index',
                    'active' => request()->routeIs('users.*'),
                    'icon' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0z',
                ] : null,
            ])),
        ],
    ];
@endphp

{{-- Mobile overlay --}}
<div
    x-show="sidebarOpen"
    x-cloak
    x-transition:enter="transition-opacity ease-out duration-150"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-in duration-100"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="sidebarOpen = false"
    class="fixed inset-0 z-40 bg-black/50 lg:hidden"
></div>

{{-- Sidebar --}}
<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed inset-y-0 left-0 z-40 w-64 border-r bg-card flex flex-col transition-transform duration-200"
>
    {{-- Brand --}}
    <div class="flex h-14 items-center gap-2 border-b px-5">
        <img src="{{ asset('img/logo.png') }}" alt="Warehaus" class="h-7 w-7">
        <span class="font-bold text-base tracking-tight">Warehaus</span>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
        @foreach($navSections as $section)
            @if(count($section['items']) > 0)
                <div>
                    <p class="mb-2 px-2 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">{{ $section['label'] }}</p>
                    <ul class="space-y-0.5">
                        @foreach($section['items'] as $item)
                            <li>
                                <a
                                    href="{{ route($item['route']) }}"
                                    class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors {{ $item['active'] ? 'bg-primary/10 text-primary font-medium' : 'text-muted-foreground hover:bg-accent hover:text-foreground' }}"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" /></svg>
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endforeach
    </nav>

    {{-- Role card + logout --}}
    <div class="border-t p-3 space-y-2">
        <div class="flex items-center gap-2.5 rounded-lg bg-accent/50 p-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary text-xs font-semibold">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium truncate">{{ $user->name }}</p>
                <p class="text-xs text-muted-foreground truncate">{{ $user->email }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="group flex w-full items-center gap-3 rounded-md px-3 py-2 text-sm text-muted-foreground transition-colors hover:bg-destructive/10 hover:text-destructive">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" /></svg>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>
