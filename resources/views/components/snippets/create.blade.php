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

<script src="//cdn.jsdelivr.net/gh/highlightjs/cdn-release@9.18.0/build/highlight.min.js"></script>
<script src="/assets/codemirror-5.51.0/lib/codemirror.js"></script>
<script>
    const snippet = document.getElementById('body')
    const editor = CodeMirror.fromTextArea(snippet, {
        theme: 'dracula',
        lineNumbers: true,
        extraKeys: {
            "Tab": function(cm){
                cm.replaceSelection("  " , "end")
            }
        }
    })
    document.querySelectorAll('.CodeMirror-line').forEach((line) => {
        hljs.highlightBlock(line)
    })
    editor.on('change', function(e) {
        document.querySelectorAll('.CodeMirror-line').forEach((line) => {
            hljs.highlightBlock(line)
        })
    })
</script>
