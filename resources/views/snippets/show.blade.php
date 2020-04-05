@extends('layouts.main')

@section('content')
    {{ $snippet->title }}
    {{ $snippet->description }}
    {!! $snippet->body !!}
@stop

