@extends('layouts.main')

@section('title')
    My Snippets
@endsection

@section('content')

    <div class="section">
        <h2 class="has-text-centered title is-2 has-text-primary">
            @if(request()->routeIs('favorite-snippets'))
                My favorite snippets
            @elseif(request()->routeIs('user.snippets'))
                My snippets
            @elseif(request()->routeIs('user.forked-snippets'))
                My snippets that was forked
            @endif
        </h2>
    </div>

    @if($snippets->total() > $snippets->perPage())
        @component('components.pagers.snippets', ['snippets' => $snippets])
        @endcomponent
    @endif

    @foreach($snippets as $snippet_index => $snippet)
        @component('components.snippets.show', ['snippet' => $snippet])
        @endcomponent
    @endforeach

    @if($snippets->total() > $snippets->perPage())
        @component('components.pagers.snippets', ['snippets' => $snippets])
        @endcomponent
    @endif
@endsection
