@foreach (['success', 'warning', 'error'] as $messageType)
    @if (session($messageType))
        <div class="alert alert-{{ $messageType === 'error' ? 'danger' : $messageType }} alert-dismissible fade show admin-alert" role="alert">
            {{ session($messageType) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
@endforeach

@if ($errors->any())
    <div class="alert alert-danger admin-alert" role="alert">
        <strong>Please correct the following:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
