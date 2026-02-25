<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($genres as $genre)
    <url>
        <loc>{{ route('genres.show', $genre->slug) }}</loc>
        <lastmod>{{ $genre->updated_at->toAtomString() }}</lastmod>
        <priority>0.6</priority>
    </url>
@endforeach
</urlset>
