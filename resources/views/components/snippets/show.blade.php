<div class="section">
    <div class="card">
        <div class="card-content">
            <article>
                <h4 class="title is-3 is-flex">
                    <a href="{{ route('snippets.show', ['snippet' => $snippet->id]) }}" class="flex">
                        {{ $snippet->title }} <span class="title is-size-7 has-text-grey">({{ $snippet->created_at->diffForHumans() }} by {{ (auth()->check() && $snippet->user->id === auth()->id()) ? 'you' : $snippet->user->name }}.)</span>
                    </a>

                    <a class="button is-primary" @auth href="{{ route('snippets.forks.create', ['snippet' => $snippet->id]) }}" @endauth title="fork this snippet" @guest disabled @endguest >Fork Me</a>
                    <span onclick="copyToClipboard('snippet-{{ $snippet->id }}')" title="copy to clipboard"><i class="fas fa-clipboard title is-size-3 has-text-info"></i></span>
                </h4>

                <pre><code id="snippet-{{ $snippet->id }}">{{ $snippet->body }}</code></pre>
            </article>
        </div>
        @if($snippet->isAFork())
            <div class="card-content">
                <a href="{{ route('snippets.show', ['snippet' => $snippet->parent->id]) }}"><span class="icon has-text-info is-3 title"><i class="fas fa-code-branch"></i></span> from {{ $snippet->parent->title }}</a>
            </div>
        @endif
        @if($snippet->forks->count())
            <div class="card-content">
                <div class="columns">
                    <div class="is-narrow">
                        <span class="icon has-text-info is-3 title"><i class="fas fa-baby"></i></span>
                    </div>
                    <div class="is-narrow">
                        <ul>
                            @foreach($snippet->forks as $iteration => $fork)
                                <li>
                                    &nbsp;- <a href="{{ route('snippets.show', ['snippet' => $fork->id]) }}">
                                        {{ $fork->title }} <span class="title is-7">({{ $snippet->created_at->diffForHumans() }} from {{ (auth()->check() && $snippet->user->id === auth()->id()) ? 'you' : $snippet->user->name }}.)</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
