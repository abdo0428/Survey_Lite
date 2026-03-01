<button {{ $attributes->merge(['type' => 'submit', 'class' => 'sl-btn sl-btn-primary']) }}>
    {{ $slot }}
</button>
