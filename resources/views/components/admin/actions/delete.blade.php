@props([
    'url',
    'permission' => null,
    'label' => 'Deactivate',
    'confirm' => 'Are you sure?',
])

@php
    $admin = auth('admin')->user();
    $allowed = $admin && (! $permission || $admin->hasPermission($permission));
@endphp

@if ($allowed)
    <form action="{{ $url }}" method="POST" data-confirm="{{ $confirm }}">
        @csrf
        @method('DELETE')
        <button type="submit" {{ $attributes->class(['btn', 'btn-sm', 'btn-outline-danger']) }}>{{ $label }}</button>
    </form>
@endif
