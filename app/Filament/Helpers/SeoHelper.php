<?php

namespace App\Filament\Helpers;

use Filament\Forms;

class SeoHelper
{
    public static function seoSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('SEO Настройки')
            ->description('Настройки для поисковых систем')
            ->collapsed()
            ->schema([
                    Forms\Components\Textarea::make('seo_description')
                        ->label('SEO Описание (Meta Description)')
                        ->rows(3)
                        ->default(fn () => \App\Models\SiteSetting::where('key', 'default_seo_description')->value('value')),
                    Forms\Components\TextInput::make('seo_keywords')
                        ->label('SEO Ключевые слова (Keywords)')
                        ->maxLength(255),
                ]);
    }
}
