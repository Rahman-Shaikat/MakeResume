@props([
    'name',
    'label',
    'value' => null,
    'required' => false,
    'accept' => null,
    'errorKey' => null,
    'help' => null,
    'wrapperClass' => null,
])

@php
    $field = $errorKey ?? $name;
    $inputId = str_replace(['.', '[', ']'], '_', $name);
@endphp

<div @if ($wrapperClass) class="{{ $wrapperClass }}" @endif>
    <label class="form-label" for="{{ $inputId }}">
        {{ $label }}
        @if ($required)
            <span class="admin-required-mark" aria-hidden="true">*</span>
        @endif
    </label>
    @if ($value)
        <a class="admin-current-file" href="{{ asset('storage/' . ltrim($value, '/')) }}" target="_blank" rel="noopener">View current file</a>
    @endif
    <input
        {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($field)]) }}
        id="{{ $inputId }}"
        name="{{ $name }}"
        type="file"
        @if ($accept) accept="{{ $accept }}" @endif
        @required($required)
    >
    @if ($help)
        <small class="admin-field-help">{{ $help }}</small>
    @endif
    <x-admin.forms.error :field="$field" />
</div>
