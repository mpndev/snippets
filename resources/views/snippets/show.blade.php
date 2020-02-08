@extends('layouts.main')

@section('title')
    Snippet
@endsection

@section('content')
    @component('components.snippets.show', ['snippet' => $snippet])
    @endcomponent
@endsection
