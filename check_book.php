<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$book = App\Models\Book::where('slug', 'enim-doloribus-consequatur-non-lv9lu')->first();

if (!$book) {
    echo "Book not found.\n";
    exit;
}

echo "Book ID: " . $book->id . "\n";
echo "Title: " . $book->title . "\n";
echo "TXT: " . ($book->file_txt ?? 'NULL') . "\n";
echo "FB2: " . ($book->file_fb2 ?? 'NULL') . "\n";
echo "EPUB: " . ($book->file_epub ?? 'NULL') . "\n";
