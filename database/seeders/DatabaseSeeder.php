<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Genre;
use App\Models\Author;
use App\Models\Series;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Support\Facades\Storage; // Needed for file generation
use Carbon\Carbon; // Needed for daily stats

use App\Models\SiteSetting;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seeding Site Settings
        SiteSetting::updateOrCreate(['key' => 'home_bottom_title'], ['value' => 'О библиотеке Librain']);
        SiteSetting::updateOrCreate(['key' => 'home_bottom_text'], ['value' => '<p>Librain - это современная электронная библиотека, где вы найдете тысячи книг различных жанров. Мы стремимся сделать чтение доступным и удобным для каждого. Наша коллекция регулярно пополняется новинками, а удобный поиск поможет вам быстро найти нужную книгу.</p><p>Присоединяйтесь к нашему сообществу читателей, оставляйте отзывы, делитесь впечатлениями и открывайте для себя новые литературные миры вместе с Librain!</p>']);
        SiteSetting::updateOrCreate(['key' => 'contact_email'], ['value' => 'support@librain.ru']);

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
                // Assign dummy paths so the UI renders the download buttons.
                // Actual generation will happen on the fly in CatalogController.
                foreach (['txt', 'fb2', 'epub'] as $format) {
                    $book->{'file_' . $format} = "books/{$book->id}.{$format}";
                }

                $book->save();

                // Assign a random series sometimes
                if (rand(0, 100) < 30 && $seriesList->count() > 0) {
                    $series = $seriesList->random();
                    $book->series()->attach($series->id, ['order' => rand(1, 10)]);
                }

                // Attach genres
                $book->genres()->attach($genres->random(rand(1, 3))->pluck('id'));

                // Generate Daily Views Stats (last year)
                $startDate = Carbon::now()->subYear();
                for ($i = 0; $i <= 365; $i++) {
                    $date = $startDate->copy()->addDays($i);
                    // 30% chance of views on any given day
                    if (rand(0, 100) < 30) {
                        \App\Models\BookDailyView::create([
                            'book_id' => $book->id,
                            'date' => $date->toDateString(),
                            'views' => rand(1, 50) // Random views per day
                        ]);
                    }
                }

                $book->views = $book->dailyViews()->sum('views');
                $book->save();

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

                // Generate reviews
                \App\Models\Review::factory(rand(0, 5))->create([
                    'book_id' => $book->id,
                    'user_id' => User::inRandomOrder()->first()->id
                ]);
            }
        });

        $this->call(PageSeeder::class);
        $this->call(SiteSettingsSeeder::class);
    }
}
