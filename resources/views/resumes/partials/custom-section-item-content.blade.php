@if (filled($item->data['content_html'] ?? null))
    <p class="{{ $class ?? '' }}">{!! $item->data['content_html'] !!}</p>
@elseif (filled($item->data['content'] ?? null))
    <p class="{{ $class ?? '' }}">{!! nl2br(e($item->data['content'])) !!}</p>
@endif
