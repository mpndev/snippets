<div class="section">
    <div class="card">
        <div class="card-content">

            <h1 class="title">@if(isset($is_fork)) Fork the snippet <a href="{{ route('snippets.show', ['snippet' => $snippet->id]) }}">"{{ $snippet->title }}"</a> @else New snippet @endif</h1>

            <div class="card-content">
                <label for="title" class="label">Title:</label>
                <input type="text" id="title" name="title" class="input" value="{{ $snippet->title ?? '' }}">
            </div>

            <div class="card-content">
                <label for="body" class="label">Body:</label>
                <textarea name="body" id="body" class="textarea">{{ $snippet->body ?? '' }}</textarea>
            </div>

            <div class="card-content">
                <input type="submit" class="button is-primary" value="Publish Snippet" title="save the snippet">
            </div>
        </div>
    </div>
</div>
