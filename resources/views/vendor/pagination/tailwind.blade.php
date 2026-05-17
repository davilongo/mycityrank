@if ($paginator->hasPages())
<nav class="xf-pagination" role="navigation" aria-label="Pagination">

    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span class="xf-page-btn xf-page-btn--disabled" aria-disabled="true">‹</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="xf-page-btn">‹</a>
    @endif

    {{-- Page numbers --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="xf-page-btn xf-page-btn--dots">…</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="xf-page-btn xf-page-btn--on" aria-current="page">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="xf-page-btn">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="xf-page-btn">›</a>
    @else
        <span class="xf-page-btn xf-page-btn--disabled" aria-disabled="true">›</span>
    @endif

</nav>
@endif
