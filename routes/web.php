<?php

use Illuminate\Support\Facades\Route;

Route::view('/ivangostev', 'developer.ivangostev')->name('developer');



Auth::routes();


Route::get('auth/{provider}/redirect', [App\Http\Controllers\Auth\SocialController::class, 'redirect'])->name('social.redirect');
Route::get('auth/{provider}/callback', [App\Http\Controllers\Auth\SocialController::class, 'callback'])->name('social.callback');


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::redirect('/catalog', '/');
Route::get('/', [App\Http\Controllers\CatalogController::class, 'index'])->name('catalog.index');
Route::get('/genres', [App\Http\Controllers\CatalogController::class, 'genres'])->name('genres.index');
Route::get('/genres/{slug}', [App\Http\Controllers\CatalogController::class, 'genre'])->name('genres.show');

Route::get('/authors', [App\Http\Controllers\CatalogController::class, 'authors'])->name('authors.index');
Route::get('/authors/{slug}', [App\Http\Controllers\CatalogController::class, 'author'])->name('authors.show');

Route::get('/series', [App\Http\Controllers\CatalogController::class, 'series'])->name('series.index');
Route::get('/series/{slug}', [App\Http\Controllers\CatalogController::class, 'seriesShow'])->name('series.show');

Route::get('/top100', [App\Http\Controllers\CatalogController::class, 'top100'])->name('top100');
Route::get('/search', [App\Http\Controllers\CatalogController::class, 'search'])->name('search');


Route::get('/library', [App\Http\Controllers\LibraryController::class, 'index'])->middleware('auth')->name('library.index');
Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->middleware('auth')->name('profile.show');
Route::get('/users/{user}', [App\Http\Controllers\ProfileController::class, 'show'])->name('users.show');
Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->middleware('auth')->name('profile.update');


Route::post('/books/{book}/favorite', [App\Http\Controllers\LibraryController::class, 'toggleFavorite'])->middleware('auth')->name('books.favorite');
Route::post('/books/{book}/planned', [App\Http\Controllers\LibraryController::class, 'togglePlanned'])->middleware('auth')->name('books.planned');
Route::post('/books/{book}/rate', [App\Http\Controllers\RatingController::class, 'store'])->name('books.rate');
Route::post('/books/{book}/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
Route::post('/books/{book}/status', [App\Http\Controllers\LibraryController::class, 'updateStatus'])->middleware('auth')->name('books.status');
Route::post('/reviews/{review}/vote', [App\Http\Controllers\ReviewController::class, 'vote'])->name('reviews.vote');
Route::get('/books/{id}/related', [App\Http\Controllers\CatalogController::class, 'related'])->name('books.related');
Route::get('/books/{book}/download/{format}', [App\Http\Controllers\CatalogController::class, 'downloadPage'])->name('books.download.page');

Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemap_pages.xml', [App\Http\Controllers\SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('/sitemap_genres.xml', [App\Http\Controllers\SitemapController::class, 'genres'])->name('sitemap.genres');
Route::get('/sitemap_authors.xml', [App\Http\Controllers\SitemapController::class, 'authors'])->name('sitemap.authors');
Route::get('/sitemap_series.xml', [App\Http\Controllers\SitemapController::class, 'series'])->name('sitemap.series');
Route::get('/sitemap_books_{index}.xml', [App\Http\Controllers\SitemapController::class, 'books'])->name('sitemap.books');
Route::get('/p/{slug}', [App\Http\Controllers\PageController::class, 'show'])->name('pages.show');


Route::get('/read/{slug}/{page?}', [App\Http\Controllers\CatalogController::class, 'read'])->name('books.read');
Route::get('/{genre}/{slug}', [App\Http\Controllers\CatalogController::class, 'book'])->name('books.show');
Route::get('/{slug}', [App\Http\Controllers\CatalogController::class, 'bookLegacy'])->name('books.show.legacy');


