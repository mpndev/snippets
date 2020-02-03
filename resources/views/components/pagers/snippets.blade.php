<div class="section">
    <nav class="pagination is-right" role="navigation" aria-label="pagination">
        <span class="tag is-light is-large">snippets {{ $snippets->count() }}/{{ $snippets->total() }}</span>
        <ul class="pagination-list">
            @if($snippets->currentPage() > 2)
                <li><a href="{{ $snippets->url('1') }}" class="pagination-link" aria-label="first page">first page</a></li>
                <li><span class="pagination-ellipsis">&hellip;</span></li>
                <li><a href="{{ $snippets->url($snippets->currentPage() - 1) }}" class="pagination-link">previous page</a></li>
                <li><a href="{{ $snippets->url($snippets->currentPage() - 2) }}" class="pagination-link" aria-label="{{ $snippets->currentPage() - 1 }}">{{ $snippets->currentPage() - 2 }}</a></li>
            @endif

            @if($snippets->currentPage() > 1)
                <li><a href="{{ $snippets->url($snippets->currentPage() - 1) }}" class="pagination-link" aria-label="{{ $snippets->currentPage() - 1 }}">{{ $snippets->currentPage() - 1 }}</a></li>
            @endif

            <li><a href="{{ $snippets->url($snippets->currentPage()) }}" class="pagination-link is-current" aria-label="Page {{ $snippets->currentPage() }}" aria-current="page">{{ $snippets->currentPage() }}</a></li>

            @if($snippets->currentPage() < $snippets->lastPage())
                <li><a href="{{ $snippets->url($snippets->currentPage() + 1) }}" class="pagination-link" aria-label="{{ $snippets->currentPage() + 1 }}">{{ $snippets->currentPage() + 1 }}</a></li>
            @endif

            @if($snippets->currentPage() < ($snippets->lastPage() - 1))
                <li><a href="{{ $snippets->url($snippets->currentPage() + 2) }}" class="pagination-link" aria-label="{{ $snippets->currentPage() + 2 }}">{{ $snippets->currentPage() + 2 }}</a></li>
                <li><a href="{{ $snippets->nextPageUrl() }}" class="pagination-link" area-label="next page">next page</a></li>
                <li><span class="pagination-ellipsis">&hellip;</span></li>
                <li><a href="{{ $snippets->url($snippets->lastPage()) }}" class="pagination-link" aria-label="last page">last page</a></li>
            @endif
        </ul>
    </nav>
</div>
