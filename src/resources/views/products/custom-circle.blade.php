@if ($paginator->hasPages())
    <nav class="pagination-nav" role="navigation" aria-label="Pagination Navigation">
        
        <div class="pagination-list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="@lang('pagination.previous')" class="pagination-prev-next">
                    &lsaquo; 前へ 
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')" class="pagination-prev-next">
                    &lsaquo; 前へ 
                </a>
            @endif

            {{-- Pagination Elements --}}
            {{-- ★ $elements に戻す: $products->links() が自動的に注入する --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span aria-disabled="true" class="pagination-dots">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')" class="pagination-prev-next">
                    次へ &rsaquo;
                </a>
            @else
                <span aria-disabled="true" aria-label="@lang('pagination.next')" class="pagination-prev-next">
                    次へ &rsaquo;
                </span>
            @endif
        </div>
    </nav>
@endif