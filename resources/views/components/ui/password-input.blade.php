@props([
    'id',
    'name',
    'autocomplete' => null,
    'placeholder' => '••••••••',
    'required' => false,
    'autofocus' => false,
    'value' => null,
])

<div x-data="{ show: false }" class="relative">
    <input
        x-bind:type="show ? 'text' : 'password'"
        id="{{ $id }}"
        name="{{ $name }}"
        @if ($value !== null) value="{{ $value }}" @endif
        @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        @if ($required) required @endif
        @if ($autofocus) autofocus @endif
        placeholder="{{ $placeholder }}"
        {{ $attributes->class([
            'flex h-9 w-full rounded-md border border-input bg-background pl-3 pr-10 py-1 text-sm shadow-sm transition-colors',
            'placeholder:text-muted-foreground',
            'hover:border-muted-foreground/40',
            'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-muted-foreground/25 focus-visible:ring-offset-1 focus-visible:ring-offset-background focus-visible:border-muted-foreground/50',
            'disabled:cursor-not-allowed disabled:opacity-50',
        ]) }}
    />

    <button
        type="button"
        @click="show = !show"
        :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
        tabindex="-1"
        class="absolute inset-y-0 right-0 flex items-center px-2.5 text-muted-foreground hover:text-foreground transition-colors focus:outline-none focus-visible:text-foreground"
    >
        {{-- Eye (password hidden, click to show) --}}
        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        {{-- Eye-off (password visible, click to hide) --}}
        <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
        </svg>
    </button>
</div>
