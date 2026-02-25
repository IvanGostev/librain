<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <sitemap>
        <loc>{{ route('sitemap.pages') }}</loc>
        <lastmod>{{ now()->startOfDay()->toAtomString() }}</lastmod>
    </sitemap>
    <sitemap>
        <loc>{{ route('sitemap.genres') }}</loc>
        <lastmod>{{ $latestGenreDate }}</lastmod>
    </sitemap>
    <sitemap>
        <loc>{{ route('sitemap.authors') }}</loc>
        <lastmod>{{ $latestAuthorDate }}</lastmod>
    </sitemap>
    <sitemap>
        <loc>{{ route('sitemap.series') }}</loc>
        <lastmod>{{ $latestSeriesDate }}</lastmod>
    </sitemap>
@for ($i = 1; $i <= $bookPages; $i++)
    <sitemap>
        <loc>{{ route('sitemap.books', ['index' => $i]) }}</loc>
        <lastmod>{{ $i == 1 ? $latestBookDate : now()->startOfDay()->toAtomString() }}</lastmod>
    </sitemap>
@endfor
</sitemapindex>
