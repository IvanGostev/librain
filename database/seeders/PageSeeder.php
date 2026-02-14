<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{

    public function run(): void
    {

        Page::updateOrCreate(
            ['slug' => 'privacy-policy'],
            [
                'title' => 'Политика конфиденциальности',
                'content' => '<p>Здесь должен быть текст вашей политики конфиденциальности.</p>',
                'is_active' => true,
            ]
        );


        Page::updateOrCreate(
            ['slug' => 'copyright'],
            [
                'title' => 'Правообладателям',
                'content' => '<p>Здесь должна быть информация для правообладателей.</p>',
                'is_active' => true,
            ]
        );
    }
}
