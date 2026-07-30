@props(['field'])

@error($field)
    <span class="admin-field-error" role="alert">{{ $message }}</span>
@enderror
