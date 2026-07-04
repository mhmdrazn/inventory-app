@props(['variant' => 'default'])

@php
    $variants = [
        'default' => 'bg-primary/10 text-primary ring-1 ring-inset ring-primary/20',
        'secondary' => 'bg-secondary text-secondary-foreground ring-1 ring-inset ring-border',
        'success' => 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 ring-1 ring-inset ring-emerald-500/20',
        'warning' => 'bg-amber-500/10 text-amber-700 dark:text-amber-400 ring-1 ring-inset ring-amber-500/20',
        'destructive' => 'bg-destructive/10 text-destructive dark:text-red-400 ring-1 ring-inset ring-destructive/20',
        'info' => 'bg-blue-500/10 text-blue-700 dark:text-blue-400 ring-1 ring-inset ring-blue-500/20',
    ];
    $classes = 'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium '.($variants[$variant] ?? $variants['default']);
@endphp

<span {{ $attributes->class($classes) }}>{{ $slot }}</span>
