@props(['padded' => true])

<div {{ $attributes->class(['rounded-lg border bg-card text-card-foreground shadow-sm', 'p-6' => $padded]) }}>
    {{ $slot }}
</div>
