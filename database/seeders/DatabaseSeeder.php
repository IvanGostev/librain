<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Genre;
use App\Models\Author;
use App\Models\Series;
use App\Models\Book;
use App\Models\Review;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Users
        User::factory()->create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        User::factory(10)->create();

        // Genres (Use the factory to create specific ones or random)
        // Since we hardcoded a list in factory, we can loop to create them if we want unique, 
        // but for now let's just create 10 distinct ones if possible or just 10 random.
        // Better:
        $genres = Genre::factory()->count(10)->create();

        // Authors
        $authors = Author::factory()->count(20)->create();

        // Series
        $seriesList = Series::factory()->count(5)->create();

        // Books
        // Create books for each author
        $authors->each(function ($author) use ($genres, $seriesList) {
            $books = Book::factory()->count(rand(2, 5))->make([
                'author_id' => $author->id,
            ]);

            foreach ($books as $book) {
                $book->save();

                // Assign a random series sometimes
                if (rand(0, 100) < 30 && $seriesList->count() > 0) {
                    $series = $seriesList->random();
                    // Attach via pivot table 'book_series'
                    // Pivot columns: series_id, book_id, order
                    // We assume Many-to-Many or One-to-Many but via pivot.
                    // If Book model doesn't have 'series' relationship defined as belongsToMany, we might need to use DB façade or define it.
                    // Assuming Book belongsToMany Series (or belongsTo if pivot is just for order? No, migration says book_series).
                    // Let's assume belongsToMany in model for 'series'.
                    $book->series()->attach($series->id, ['order' => rand(1, 10)]);
                }

                // Attach genres
                $book->genres()->attach($genres->random(rand(1, 3))->pluck('id'));

                // Create chapters
                $chapterCount = rand(5, 15);
                for ($i = 1; $i <= $chapterCount; $i++) {
                    \App\Models\Chapter::factory()->create([
                        'book_id' => $book->id,
                        'order' => $i,
                        'title' => "Глава $i: " . fake()->realText(30),
                        'content' => fake()->realText(2000),
                    ]);
                }
            }
        });
    }
}
