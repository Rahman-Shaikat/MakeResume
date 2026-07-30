@props([
    'name',
    'label',
    'value' => 1,
    'checked' => false,
    'description' => null,
    'errorKey' => null,
    'inputAttributes' => [],
])

@php
    $field = $errorKey ?? $name;
    $inputId = str_replace(['.', '[', ']'], '_', $name . '_' . $value);
    $checkboxAttributes = new Illuminate\View\ComponentAttributeBag($inputAttributes);
@endphp

<label {{ $attributes->class(['form-check']) }}>
    <input
        {{ $checkboxAttributes->class(['form-check-input', 'is-invalid' => $errors->has($field)]) }}
        id="{{ $inputId }}"
        type="checkbox"
        name="{{ $name }}"
        value="{{ $value }}"
        @checked($checked)
    >
    <span>
        @if ($description)
            <strong>{{ $label }}</strong>
            <small>{{ $description }}</small>
        @else
            {{ $label }}
        @endif
    </span>
</label>
