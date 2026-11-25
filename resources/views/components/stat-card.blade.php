@props([
    'label',
    'value',
    'icon',
    'tone' => 'primary',
    'hint' => null,
])

<section {{ $attributes->class(['stat-card', 'tone-' . $tone]) }}>
    <div>
        <h2 class="stat-label">{{ $label }}</h2>
        <p class="stat-value">{{ $value }}</p>

        @if ($hint)
            <p class="stat-hint">{{ $hint }}</p>
        @endif
    </div>

    <span class="stat-icon">
        <i class="bi bi-{{ $icon }}" aria-hidden="true"></i>
    </span>
</section>