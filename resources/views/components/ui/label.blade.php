@props(['for' => null, 'value' => null])

<label @if($for) for="{{ $for }}" @endif {{ $attributes->class(['text-sm font-medium leading-none text-foreground']) }}>
    {{ $value ?? $slot }}
</label>
