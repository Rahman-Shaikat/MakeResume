@props([
    'name',
    'label',
    'value' => null,
    'required' => false,
    'placeholder' => null,
    'rows' => 4,
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
    <textarea
        {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($field)]) }}
        id="{{ $inputId }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if (! is_null($placeholder)) placeholder="{{ $placeholder }}" @endif
        @required($required)
    >{{ $value }}</textarea>
    @if ($help)
        <small class="admin-field-help">{{ $help }}</small>
    @endif
    <x-admin.forms.error :field="$field" />
</div>
