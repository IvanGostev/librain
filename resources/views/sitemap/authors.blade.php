<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($authors as $author)
    <url>
        <loc>{{ route('authors.show', $author->slug) }}</loc>
        <lastmod>{{ $author->updated_at->toAtomString() }}</lastmod>
        <priority>0.6</priority>
    </url>
@endforeach
</urlset>
