@props(['value'])

<label {{ $attributes->merge(['class' => 'sl-label']) }}>
    {{ $value ?? $slot }}
</label>
