<?php

namespace App\Filament\Helpers;

use Filament\Forms;
use App\Filament\Forms\Components\SeoTemplateInput;

class SeoHelper
{
    public static function seoSection(string $entityType = 'book'): Forms\Components\Section
    {
        $variables = [];
        $defaultTitle = '';
        $defaultDesc = '';

        if ($entityType === 'book') {
            $variables = ['{title}' => 'Название книги', '{author}' => 'Автор', '{genres}' => 'Жанры', '{year}' => 'Год издания'];
            $defaultTitle = \App\Models\SiteSetting::where('key', 'tpl_seo_title_book')->value('value') ?? '{title} — Читать книгу онлайн';
            $defaultDesc = \App\Models\SiteSetting::where('key', 'tpl_seo_desc_book')->value('value') ?? '{title} — читать онлайн или скачать в форматах fb2, epub, txt.';
        } elseif ($entityType === 'author') {
            $variables = ['{name}' => 'Имя автора'];
            $defaultTitle = \App\Models\SiteSetting::where('key', 'tpl_seo_title_author')->value('value') ?? '{name} — Книги автора читать онлайн';
            $defaultDesc = \App\Models\SiteSetting::where('key', 'tpl_seo_desc_author')->value('value') ?? '{name} — читать лучшие книги онлайн.';
        } elseif ($entityType === 'genre') {
            $variables = ['{name}' => 'Название жанра'];
            $defaultTitle = \App\Models\SiteSetting::where('key', 'tpl_seo_title_genre')->value('value') ?? '{name} — Книги жанра онлайн';
            $defaultDesc = \App\Models\SiteSetting::where('key', 'tpl_seo_desc_genre')->value('value') ?? '{name} — большая библиотека произведений онлайн.';
        } elseif ($entityType === 'series') {
            $variables = ['{name}' => 'Название серии'];
            $defaultTitle = \App\Models\SiteSetting::where('key', 'tpl_seo_title_series')->value('value') ?? '{name} — Книжная серия читать';
            $defaultDesc = \App\Models\SiteSetting::where('key', 'tpl_seo_desc_series')->value('value') ?? '{name} — читать книги серии по порядку.';
        }

        $settingsVariables = [];
        foreach (\App\Models\SiteSetting::all() as $setting) {
            $settingsVariables['{setting:' . $setting->key . '}'] = 'Настройка: ' . $setting->key;
        }
        $variables = array_merge($variables, $settingsVariables);

        return Forms\Components\Section::make('SEO Настройки')
            ->description('Настройки для поисковых систем. Оставьте пустым для использования глобального шаблона.')
            ->collapsed()
            ->schema([
                SeoTemplateInput::make('seo_title')
                    ->label('SEO Заголовок (Title)')
                    ->variables($variables)
                    ->placeholder("Глобальный шаблон: " . $defaultTitle)
                    ->default($defaultTitle)
                    ->nullable(),
                    
                SeoTemplateInput::make('seo_description')
                    ->label('SEO Описание (Meta Description)')
                    ->multiline()
                    ->variables($variables)
                    ->placeholder("Глобальный шаблон: " . $defaultDesc)
                    ->default($defaultDesc)
                    ->nullable(),
            ]);
    }
}
