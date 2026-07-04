@props(['variant' => 'default'])

@php
    $variants = [
        'default' => 'bg-card border-border text-foreground',
        'success' => 'bg-emerald-500/5 border-emerald-500/20 text-emerald-800 dark:text-emerald-300',
        'warning' => 'bg-amber-500/5 border-amber-500/20 text-amber-900 dark:text-amber-300',
        'destructive' => 'bg-destructive/5 border-destructive/20 text-destructive dark:text-red-300',
        'info' => 'bg-blue-500/5 border-blue-500/20 text-blue-900 dark:text-blue-300',
    ];
    $classes = 'relative w-full rounded-lg border p-4 text-sm '.($variants[$variant] ?? $variants['default']);
@endphp

<div {{ $attributes->class($classes) }} role="alert">
    {{ $slot }}
</div>
