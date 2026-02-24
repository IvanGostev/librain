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
        SiteSetting::updateOrCreate(['key' => 'home_bottom_title'], ['value' => 'О библиотеке Librain']);
        SiteSetting::updateOrCreate(['key' => 'home_bottom_text'], ['value' => '<p>Librain - это современная электронная библиотека, где вы найдете тысячи книг различных жанров. Мы стремимся сделать чтение доступным и удобным для каждого. Наша коллекция регулярно пополняется новинками, а удобный поиск поможет вам быстро найти нужную книгу.</p><p>Присоединяйтесь к нашему сообществу читателей, оставляйте отзывы, делитесь впечатлениями и открывайте для себя новые литературные миры вместе с Librain!</p>']);
        SiteSetting::updateOrCreate(['key' => 'contact_email'], ['value' => 'support@librain.ru']);

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'username' => 'testuser',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );
        User::factory(10)->create();

        $genres = Genre::factory()->count(10)->create();

        $authors = Author::factory()->count(20)->create();

        $seriesList = Series::factory()->count(5)->create();

        $books = Book::factory()->count(60)->make();

        foreach ($books as $book) {
                foreach (['txt', 'fb2', 'epub'] as $format) {
                    $book->{'file_' . $format} = "books/{$book->id}.{$format}";
                }

                $book->save();
                
                $book->authors()->attach($authors->random(rand(1, 3))->pluck('id'));

                if (rand(0, 100) < 30 && $seriesList->count() > 0) {
                    $series = $seriesList->random();
                    $book->series()->attach($series->id, ['order' => rand(1, 10)]);
                }

                $book->genres()->attach($genres->random(rand(1, 3))->pluck('id'));

                $startDate = Carbon::now()->subYear();
                for ($i = 0; $i <= 365; $i++) {
                    $date = $startDate->copy()->addDays($i);
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

                $targetPages = rand(4, 10);
                $totalWordsNeeded = $targetPages * 2500;
                $chapterCount = rand(5, 10);
                $wordsPerChapter = max(100, (int)($totalWordsNeeded / $chapterCount));

                for ($i = 1; $i <= $chapterCount; $i++) {
                    $paragraphs = [];
                    $currentWords = 0;
                    while ($currentWords < $wordsPerChapter) {
                        $paragraph = fake()->realText(rand(800, 1500));
                        $paragraphs[] = $paragraph;
                        $currentWords += count(preg_split('/\s+/u', $paragraph, -1, PREG_SPLIT_NO_EMPTY));
                    }
                    $content = implode("\n\n", $paragraphs);

                    \App\Models\Chapter::factory()->create([
                        'book_id' => $book->id,
                        'order' => $i,
                        'title' => "Глава $i: " . fake()->realText(30),
                        'content' => $content,
                    ]);
                }

                \App\Models\Review::factory(rand(0, 5))->create([
                    'book_id' => $book->id,
                    'user_id' => User::inRandomOrder()->first()->id
                ]);
            }

        $this->call(PageSeeder::class);
        $this->call(SiteSettingsSeeder::class);
    }
}
