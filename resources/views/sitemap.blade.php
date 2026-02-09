<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->startOfMonth()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ url('/catalog') }}</loc>
        <lastmod>{{ now()->startOfDay()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ url('/top/100') }}</loc>
        <lastmod>{{ now()->startOfDay()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>{{ url('/genres') }}</loc>
        <lastmod>{{ now()->startOfDay()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>{{ url('/authors') }}</loc>
        <lastmod>{{ now()->startOfDay()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>{{ url('/series') }}</loc>
        <lastmod>{{ now()->startOfDay()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.7</priority>
    </url>
@foreach ($genres as $genre)
    <url>
        <loc>{{ route('genres.show', $genre->slug) }}</loc>
        <lastmod>{{ $genre->updated_at->toAtomString() }}</lastmod>
        <priority>0.6</priority>
    </url>
@endforeach
@foreach ($authors as $author)
    <url>
        <loc>{{ route('authors.show', $author->slug) }}</loc>
        <lastmod>{{ $author->updated_at->toAtomString() }}</lastmod>
        <priority>0.6</priority>
    </url>
@endforeach
@foreach ($series as $item)
    <url>
        <loc>{{ route('series.show', $item->slug) }}</loc>
        <lastmod>{{ $item->updated_at->toAtomString() }}</lastmod>
        <priority>0.7</priority>
    </url>
@endforeach
@foreach ($books as $book)
    <url>
        <loc>{{ route('books.show', $book->slug) }}</loc>
        <lastmod>{{ $book->updated_at->toAtomString() }}</lastmod>
        <priority>0.8</priority>
    </url>
@endforeach
</urlset>
