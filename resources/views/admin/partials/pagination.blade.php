@if ($paginator->hasPages())
    <nav class="admin-pagination" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span>Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}">Previous</a>
        @endif
        <small>Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</small>
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}">Next</a>
        @else
            <span>Next</span>
        @endif
    </nav>
@endif
