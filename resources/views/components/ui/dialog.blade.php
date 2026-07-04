@props([
    'name' => 'dialog',
    'title' => null,
    'description' => null,
    'maxWidth' => '2xl',
])

@php
    $maxWidthClass = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl',
        '4xl' => 'sm:max-w-4xl',
    ][$maxWidth];
@endphp

<div
    x-data="{ open: false }"
    x-on:open-dialog.window="if ($event.detail === '{{ $name }}') open = true"
    x-on:close-dialog.window="if ($event.detail === '{{ $name }}') open = false"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
>
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm"
        x-on:click="open = false"
    ></div>

    <div class="relative flex min-h-screen items-center justify-center p-4">
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full {{ $maxWidthClass }} rounded-lg border bg-card text-card-foreground shadow-lg"
            x-on:click.stop
        >
            @if($title || $description)
                <div class="flex flex-col gap-1.5 border-b p-6 pb-4">
                    @if($title)
                        <h2 class="text-lg font-semibold leading-none tracking-tight">{{ $title }}</h2>
                    @endif
                    @if($description)
                        <p class="text-sm text-muted-foreground">{{ $description }}</p>
                    @endif
                </div>
            @endif

            <button
                type="button"
                x-on:click="open = false"
                class="absolute right-4 top-4 rounded-sm opacity-70 ring-offset-background transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                aria-label="Close"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
            </button>

            <div class="p-6 {{ $title || $description ? 'pt-4' : '' }}">
                {{ $slot }}
            </div>

            @isset($footer)
                <div class="flex items-center justify-end gap-2 border-t p-6 pt-4">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
