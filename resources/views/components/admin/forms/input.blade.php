@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'required' => false,
    'placeholder' => null,
    'errorKey' => null,
    'help' => null,
    'wrapperClass' => null,
    'id' => null,
])

@php
    $field = $errorKey ?? $name;
    $inputId = $id ?? str_replace(['.', '[', ']'], '_', $name);
@endphp

<div @if ($wrapperClass) class="{{ $wrapperClass }}" @endif>
    <label class="form-label" for="{{ $inputId }}">
        {{ $label }}
        @if ($required)
            <span class="admin-required-mark" aria-hidden="true">*</span>
        @endif
    </label>
    <input
        {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($field)]) }}
        id="{{ $inputId }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if (! is_null($value)) value="{{ $value }}" @endif
        @if (! is_null($placeholder)) placeholder="{{ $placeholder }}" @endif
        @required($required)
    >
    @if ($help)
        <small class="admin-field-help">{{ $help }}</small>
    @endif
    <x-admin.forms.error :field="$field" />
</div>
