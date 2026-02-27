<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SiteSetting::updateOrCreate(['key' => 'home_bottom_title'], ['value' => 'О библиотеке Librain']);
        SiteSetting::updateOrCreate(['key' => 'home_bottom_text'], ['value' => '<p>Librain - это современная электронная библиотека...</p>']);
        
        SiteSetting::updateOrCreate(['key' => 'genres_bottom_title'], ['value' => 'Книги по жанрам']);
        SiteSetting::updateOrCreate(['key' => 'genres_bottom_text'], ['value' => '<p>Выберите интересующий вас жанр, чтобы найти лучшую литературу...</p>']);
        
        SiteSetting::updateOrCreate(['key' => 'authors_bottom_title'], ['value' => 'Наши Авторы']);
        SiteSetting::updateOrCreate(['key' => 'authors_bottom_text'], ['value' => '<p>Знакомьтесь с биографиями писателей и их произведениями.</p>']);
        
        SiteSetting::updateOrCreate(['key' => 'series_bottom_title'], ['value' => 'Книжные Серии']);
        SiteSetting::updateOrCreate(['key' => 'series_bottom_text'], ['value' => '<p>Читайте любимые книги по порядку из популярных серий.</p>']);
        
        SiteSetting::updateOrCreate(['key' => 'top100_bottom_title'], ['value' => 'Топ-100 Книг']);
        SiteSetting::updateOrCreate(['key' => 'top100_bottom_text'], ['value' => '<p>Самые популярные и читаемые книги нашей библиотеки, собранные в один рейтинг по просмотрам и оценкам.</p>']);
        
        SiteSetting::updateOrCreate(['key' => 'contact_email'], ['value' => 'support@librain.ru']);
        SiteSetting::updateOrCreate(['key' => 'seo_title_book_default'], ['value' => '— Читать книгу онлайн']);
        SiteSetting::updateOrCreate(['key' => 'seo_title_author_default'], ['value' => '— Книги автора читать онлайн']);
        SiteSetting::updateOrCreate(['key' => 'seo_title_genre_default'], ['value' => '— Книги жанра онлайн']);
        SiteSetting::updateOrCreate(['key' => 'seo_title_series_default'], ['value' => '— Книжная серия читать']);

        SiteSetting::updateOrCreate(['key' => 'seo_desc_book_default'], ['value' => '— читать онлайн или скачать в форматах fb2, epub, txt.']);
        SiteSetting::updateOrCreate(['key' => 'seo_desc_author_default'], ['value' => '— читать лучшие книги онлайн.']);
        SiteSetting::updateOrCreate(['key' => 'seo_desc_genre_default'], ['value' => '— большая библиотека произведений онлайн.']);
        SiteSetting::updateOrCreate(['key' => 'seo_desc_series_default'], ['value' => '— читать книги серии по порядку.']);
    }
}
