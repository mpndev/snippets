@extends('layouts.main')

@section('title')
    Snippet
@endsection

@section('content')
    @component('components.snippets.show', ['snippet' => $snippet])
    @endcomponent

    <div class="section">
        @component('components.navigation')
        @endcomponent
    </div>
@endsection
