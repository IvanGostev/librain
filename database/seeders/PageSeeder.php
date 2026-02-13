<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Page::firstOrCreate(
            ['slug' => 'copyright-holders'],
            [
                'title' => 'Правообладателям',
                'content' => '<h1>Информация для правообладателей</h1><p>Если вы являетесь правообладателем...</p>',
                'is_active' => true,
            ]
        );

        Page::firstOrCreate(
            ['slug' => 'privacy-policy'],
            [
                'title' => 'Политика конфиденциальности',
                'content' => '<h1>Политика конфиденциальности</h1><p>Наша политика обработки персональных данных...</p>',
                'is_active' => true,
            ]
        );

        SiteSetting::firstOrCreate(
            ['key' => 'contact_email'],
            ['value' => 'support@librain.com']
        );
    }
}
