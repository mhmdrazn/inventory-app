@props(['type' => 'text'])

<input
    type="{{ $type }}"
    {{ $attributes->class([
        'flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors',
        'placeholder:text-muted-foreground',
        'hover:border-muted-foreground/40',
        'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-muted-foreground/25 focus-visible:ring-offset-1 focus-visible:ring-offset-background focus-visible:border-muted-foreground/50',
        'disabled:cursor-not-allowed disabled:opacity-50',
        'file:border-0 file:bg-transparent file:text-sm file:font-medium',
    ]) }}
/>
