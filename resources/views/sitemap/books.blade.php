<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($books as $book)
    <url>
        <loc>{{ route('books.show', ['genre' => $book->genre_slug, 'slug' => $book->slug]) }}</loc>
        <lastmod>{{ $book->updated_at->toAtomString() }}</lastmod>
        <priority>0.8</priority>
    </url>
@endforeach
</urlset>
