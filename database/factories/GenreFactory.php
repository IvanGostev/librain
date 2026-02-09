<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Genre>
 */
class GenreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $genre = fake()->unique()->randomElement([
            'Фантастика',
            'Фэнтези',
            'Детектив',
            'Роман',
            'Триллер',
            'Ужасы',
            'Приключения',
            'Попаданцы',
            'ЛитРПГ',
            'Боевик',
            'Мистика',
            'История',
            'Научная фантастика',
            'Киберпанк',
            'Стимпанк',
            'Постапокалипсис'
        ]);

        return [
            'name' => $genre,
            'slug' => Str::slug($genre),
            'description' => fake()->paragraph(),
        ];
    }
}
