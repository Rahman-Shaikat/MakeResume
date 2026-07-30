@props([
    'name',
    'label',
    'value' => null,
    'options' => [],
    'optionValue' => 'id',
    'optionLabel' => 'name',
    'required' => false,
    'placeholder' => null,
    'placeholderValue' => '',
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
    <select
        {{ $attributes->class(['form-select', 'is-invalid' => $errors->has($field)]) }}
        id="{{ $inputId }}"
        name="{{ $name }}"
        @required($required)
    >
        @if (! is_null($placeholder))
            <option value="{{ $placeholderValue }}" @selected((string) $value === (string) $placeholderValue)>{{ $placeholder }}</option>
        @endif
        @foreach ($options as $option)
            @php
                $currentValue = data_get($option, $optionValue);
                $currentLabel = data_get($option, $optionLabel);
            @endphp
            <option value="{{ $currentValue }}" @selected((string) $value === (string) $currentValue)>{{ $currentLabel }}</option>
        @endforeach
        {{ $slot }}
    </select>
    @if ($help)
        <small class="admin-field-help">{{ $help }}</small>
    @endif
    <x-admin.forms.error :field="$field" />
</div>
