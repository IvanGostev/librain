<?php

namespace App\Filament\Helpers;

use Filament\Forms;

class SeoHelper
{
    public static function seoSection(string $entityType = 'book'): Forms\Components\Section
    {
        return Forms\Components\Section::make('SEO Настройки')
            ->description('Настройки для поисковых систем')
            ->collapsed()
            ->schema([
                    Forms\Components\Select::make('seo_title')
                        ->label('SEO Заголовок (Title)')
                        ->options(function () use ($entityType) {
                            $settings = \App\Models\SiteSetting::where('key', 'like', "seo_title_{$entityType}_%")->get();
                            $options = [];
                            foreach ($settings as $setting) {
                                $options[$setting->key] = 'Название + ' . $setting->value;
                            }
                            return $options;
                        })
                        ->default("seo_title_{$entityType}_default")
                        ->helperText('Заголовок страницы: Название + выбранная константа. Константы добавляются в "Настройки сайта" (ключ: seo_title_' . $entityType . '_...).'),
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
