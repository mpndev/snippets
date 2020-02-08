@if($errors->any())
    <div class="card-content">
        <ul>
            @foreach($errors->all() as $error)
                <li class="has-text-danger">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
