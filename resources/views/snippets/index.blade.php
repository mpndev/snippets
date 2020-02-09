@extends('layouts.main')

@section('title')
    Snippets
@endsection

@section('content')
    @foreach($snippets as $snippet)
        @component('components.snippets.show', ['snippet' => $snippet])
        @endcomponent
    @endforeach

    @if($snippets->total() > $snippets->perPage())
        @component('components.pagers.snippets', ['snippets' => $snippets])
        @endcomponent
    @endif
@endsection
