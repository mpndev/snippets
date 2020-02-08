@extends('layouts.main')

@section('content')
<div class="section">
    <div class="card">
        <div class="card-content">
            @include('components.errors')
            <form method="POST" action="{{ route('login') }}">

                @csrf

                <div class="card-content">
                    <label for="name" class="label">name</label>
                    <input type="text" id="name" name="name" class="input" value="{{ old('name') }}">
                </div>

                <div class="card-content">
                    <label for="password" class="label">password</label>
                    <input type="password" id="password" name="password" class="input">
                </div>

                <div class="card-content">
                    <input type="submit" class="button is-primary" value="Login" title="login">
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
