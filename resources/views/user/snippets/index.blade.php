@extends('layouts.main')

@section('title')
    My Snippets
@endsection

@section('content')
    @foreach($snippets as $snippet_index => $snippet)
        @component('components.snippets.show', ['snippet' => $snippet])
        @endcomponent
    @endforeach

    @if($snippets->total() > $snippets->perPage())
        @component('components.pagers.snippets', ['snippets' => $snippets])
        @endcomponent
    @endif
@endsection
