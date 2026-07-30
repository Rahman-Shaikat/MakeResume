@props([
    'name',
    'label',
    'value' => null,
    'options' => [],
    'required' => false,
    'errorKey' => null,
    'help' => null,
    'wrapperClass' => null,
])

@php
    $field = $errorKey ?? $name;
    $selectedValue = old($field, $value);
    $fieldId = str_replace(['.', '[', ']'], '_', $name);
@endphp

<fieldset {{ $attributes->class(['admin-radio-fieldset', 'is-invalid' => $errors->has($field), $wrapperClass]) }}>
    <legend class="form-label">
        {{ $label }}
        @if ($required)
            <span class="admin-required-mark" aria-hidden="true">*</span>
        @endif
    </legend>
    <div class="admin-radio-group">
        @foreach ($options as $option)
            @php
                $optionValue = data_get($option, 'value');
                $optionId = $fieldId . '_' . $optionValue;
            @endphp
            <label class="admin-radio-option" for="{{ $optionId }}">
                <input
                    id="{{ $optionId }}"
                    name="{{ $name }}"
                    type="radio"
                    value="{{ $optionValue }}"
                    @checked((string) $selectedValue === (string) $optionValue)
                    @required($required)
                >
                <span>
                    <strong>{{ data_get($option, 'label') }}</strong>
                    @if (data_get($option, 'description'))
                        <small>{{ data_get($option, 'description') }}</small>
                    @endif
                </span>
            </label>
        @endforeach
    </div>
    @if ($help)
        <small class="admin-field-help">{{ $help }}</small>
    @endif
    <x-admin.forms.error :field="$field" />
</fieldset>
