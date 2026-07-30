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
    'searchPlaceholder' => null,
    'errorKey' => null,
    'help' => null,
    'wrapperClass' => null,
])

<x-admin.forms.select
    :name="$name"
    :label="$label"
    :value="$value"
    :options="$options"
    :option-value="$optionValue"
    :option-label="$optionLabel"
    :required="$required"
    :placeholder="$placeholder"
    :placeholder-value="$placeholderValue"
    :error-key="$errorKey"
    :help="$help"
    :wrapper-class="$wrapperClass"
    {{ $attributes->class(['js-category-parent-select'])->merge(['data-placeholder' => $searchPlaceholder ?? $placeholder]) }}
/>
