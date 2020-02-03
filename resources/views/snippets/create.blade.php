@extends('layouts.main')

@section('title')
    Create snippet
@endsection

@section('content')
    <form action="{{route('snippets.store')}}" method="POST">

        {{ csrf_field() }}

        @component('components.snippets.create')
        @endcomponent
    </form>

    <div class="section">
        @component('components.navigation')
        @endcomponent
    </div>
@endsection
