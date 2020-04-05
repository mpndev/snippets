@extends('layouts.main')

@section('content')
    <form action="{{ $snippet->is_editing ? $snippet->update_path : $snippet->store_path }}" method="POST">

        @csrf

        @if($snippet->is_editing)
            @method('PUT')
        @endif

        <input type="hidden" name="_parent_id" value="{{ $snippet->id }}">

        <input type="text" name="title" value="{{ $snippet->title }}">

        <textarea name="description" cols="30" rows="2">{{ $snippet->description }}</textarea>

        <textarea name="body" cols="30" rows="40">{!! $snippet->body !!}</textarea>

        <input type="submit" name="submit">

    </form>
@stop
