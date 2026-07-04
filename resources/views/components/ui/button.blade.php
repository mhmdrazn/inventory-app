@props([
    'variant' => 'default',
    'size' => 'default',
    'type' => 'button',
    'href' => null,
    'icon' => false,
])

@php
    $base = 'inline-flex items-center justify-center gap-2 whitespace-nowrap font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background disabled:pointer-events-none disabled:opacity-50 rounded-md';

    $variants = [
        'default' => 'bg-primary text-primary-foreground hover:bg-primary/90 shadow-sm',
        'secondary' => 'bg-secondary text-secondary-foreground hover:bg-secondary/80 shadow-sm',
        'outline' => 'border border-input bg-background hover:bg-accent hover:text-accent-foreground shadow-sm',
        'ghost' => 'hover:bg-accent hover:text-accent-foreground',
        'destructive' => 'bg-destructive text-destructive-foreground hover:bg-destructive/90 shadow-sm',
        'soft-destructive' => 'bg-destructive/10 text-destructive hover:bg-destructive/20 ring-1 ring-inset ring-destructive/20',
        'link' => 'text-primary underline-offset-4 hover:underline',
    ];

    $sizes = [
        'default' => $icon ? 'h-9 w-9' : 'h-9 px-4 text-sm',
        'sm' => $icon ? 'h-8 w-8' : 'h-8 px-3 text-xs',
        'lg' => $icon ? 'h-10 w-10' : 'h-10 px-6 text-sm',
    ];

    $classes = trim($base.' '.($variants[$variant] ?? $variants['default']).' '.($sizes[$size] ?? $sizes['default']));
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
