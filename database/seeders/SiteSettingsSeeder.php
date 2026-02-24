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
    }
}
