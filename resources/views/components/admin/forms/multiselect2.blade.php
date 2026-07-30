@props([
    'name',
    'label',
    'values' => [],
    'options' => [],
    'optionValue' => 'id',
    'optionLabel' => 'name',
    'errorKey' => null,
    'help' => null,
    'wrapperClass' => null,
    'placeholder' => null,
])

@php
    $field = $errorKey ?? rtrim($name, '[]');
    $inputId = str_replace(['.', '[', ']'], '_', $name);
    $selectedValues = collect(old($field, $values))->map(fn ($value) => (string) $value)->all();
@endphp

<div @if ($wrapperClass) class="{{ $wrapperClass }}" @endif>
    <label class="form-label" for="{{ $inputId }}">{{ $label }}</label>
    <select
        {{ $attributes->class(['form-select', 'js-admin-multiselect', 'is-invalid' => $errors->has($field)])
            ->merge(['data-placeholder' => $placeholder]) }}
        id="{{ $inputId }}"
        name="{{ $name }}"
        multiple
    >
        @foreach ($options as $option)
            @php($currentValue = (string) data_get($option, $optionValue))
            <option value="{{ $currentValue }}" @selected(in_array($currentValue, $selectedValues, true))>
                {{ data_get($option, $optionLabel) }}
            </option>
        @endforeach
    </select>
    @if ($help)
        <small class="admin-field-help">{{ $help }}</small>
    @endif
    <x-admin.forms.error :field="$field" />
</div>
