<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateBookRatings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:update-ratings';
    protected $description = 'Updates book ratings based on average review ratings';

    public function handle()
    {
        $books = \App\Models\Book::all();
        foreach ($books as $book) {
            $average = $book->reviews()
                ->whereNull('parent_id')
                ->whereNotNull('rating')
                ->avg('rating');
            $book->update(['rating' => round($average, 1) ?: 0]);
            $this->info("Updated {$book->title} rating to {$book->rating}");
        }
    }
}
