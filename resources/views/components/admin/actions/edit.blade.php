@props([
    'url',
    'permission' => null,
    'label' => 'Edit',
])

@php
    $admin = auth('admin')->user();
    $allowed = $admin && (! $permission || $admin->hasPermission($permission));
@endphp

@if ($allowed)
    <a href="{{ $url }}" title="{{ $label }}" {{ $attributes->class(['btn', 'btn-sm', 'btn-light']) }}>{{ $label }}</a>
@endif
