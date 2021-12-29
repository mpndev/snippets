<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($snippets as $snippet)
        <url>
            <loc>{{ url('/') }}/snippets/{{ $snippet->slug }}</loc>
            <lastmod>{{ $snippet->created_at->tz('UTC')->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
    <url>
        <loc>{{ url('/') }}/tags</loc>
        <lastmod>{{ $tags_last_modified->created_at->tz('UTC')->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.5</priority>
    </url>
</urlset>
