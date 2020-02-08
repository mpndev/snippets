@extends('layouts.main')

@section('title')
    Create fork
@endsection

@section('content')
    <form action="{{route('snippets.forks.store', ['snippet' => $snippet->id])}}" method="POST">

        {{ csrf_field() }}

        @component('components.snippets.create', ['snippet' => $snippet, 'is_fork' => true])
        @endcomponent

    </form>
@endsection
