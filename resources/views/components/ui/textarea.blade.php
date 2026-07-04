<textarea
    {{ $attributes->class([
        'flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm transition-colors',
        'placeholder:text-muted-foreground',
        'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-1 focus-visible:ring-offset-background focus-visible:border-ring',
        'disabled:cursor-not-allowed disabled:opacity-50',
    ]) }}
>{{ $slot }}</textarea>
