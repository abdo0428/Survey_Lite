<button {{ $attributes->merge(['type' => 'submit', 'class' => 'sl-btn sl-btn-danger']) }}>
    {{ $slot }}
</button>
