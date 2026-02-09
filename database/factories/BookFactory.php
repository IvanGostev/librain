<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Series;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);
        $title = fake()->sentence(3);
        return [
            'author_id' => Author::factory(),
            'title' => $title,
            'slug' => Str::slug($title . ' ' . Str::random(5)),
            'description' => fake()->paragraphs(5, true),
            'cover_image' => null,
            'status' => fake()->randomElement(['writing', 'finished']),
            'age_rating' => fake()->randomElement(['0+', '6+', '12+', '16+', '18+']),
            'is_published' => true,
            'views' => fake()->numberBetween(0, 50000),
            // 'published_at' is not in migration based on file view, it has timestamps only. 
            // Wait, migration 2026_02_06_175345_create_books_table.php line 23 shows is_published, but NO published_at column.
        ];
    }
}
