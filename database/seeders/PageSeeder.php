<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Privacy Policy
        Page::updateOrCreate(
            ['slug' => 'privacy-policy'],
            [
                'title' => 'Политика конфиденциальности',
                'content' => '<h1>Политика конфиденциальности</h1><p>Здесь должен быть текст вашей политики конфиденциальности.</p>',
                'is_active' => true,
            ]
        );

        // Copyright (Rights Holders)
        Page::updateOrCreate(
            ['slug' => 'copyright'],
            [
                'title' => 'Правообладателям',
                'content' => '<h1>Правообладателям</h1><p>Здесь должна быть информация для правообладателей.</p>',
                'is_active' => true,
            ]
        );
    }
}
