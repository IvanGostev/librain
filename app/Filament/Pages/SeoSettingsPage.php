<?php

namespace App\Filament\Pages;

use App\Filament\Forms\Components\SeoTemplateInput;
use App\Models\SiteSetting;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Actions\Action;

class SeoSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $navigationLabel = 'SEO Шаблоны';
    protected static ?string $title = 'Настройка SEO Шаблонов';
    protected static ?string $navigationGroup = 'Настройки';

    protected static string $view = 'filament.pages.seo-settings-page';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'seo_title_book' => SiteSetting::where('key', 'tpl_seo_title_book')->value('value') ?? '{title} — Читать книгу онлайн',
            'seo_desc_book' => SiteSetting::where('key', 'tpl_seo_desc_book')->value('value') ?? '{title} — читать онлайн или скачать в форматах fb2, epub, txt.',
            
            'seo_title_author' => SiteSetting::where('key', 'tpl_seo_title_author')->value('value') ?? '{name} — Книги автора читать онлайн',
            'seo_desc_author' => SiteSetting::where('key', 'tpl_seo_desc_author')->value('value') ?? '{name} — читать лучшие книги онлайн.',
            
            'seo_title_genre' => SiteSetting::where('key', 'tpl_seo_title_genre')->value('value') ?? '{name} — Книги жанра онлайн',
            'seo_desc_genre' => SiteSetting::where('key', 'tpl_seo_desc_genre')->value('value') ?? '{name} — большая библиотека произведений онлайн.',
            
            'seo_title_series' => SiteSetting::where('key', 'tpl_seo_title_series')->value('value') ?? '{name} — Книжная серия читать',
            'seo_desc_series' => SiteSetting::where('key', 'tpl_seo_desc_series')->value('value') ?? '{name} — читать книги серии по порядку.',
        ]);
    }

    public function form(Form $form): Form
    {
        $settingsVariables = [];
        foreach (SiteSetting::all() as $setting) {
            $settingsVariables['{setting:' . $setting->key . '}'] = 'Настройка: ' . $setting->key;
        }

        return $form
            ->schema([
                Tabs::make('SEO Settings')
                    ->tabs([
                        Tabs\Tab::make('Книги')
                            ->schema([
                                SeoTemplateInput::make('seo_title_book')
                                    ->label('SEO-заголовок')
                                    ->variables(array_merge(['{title}' => 'Название книги', '{author}' => 'Автор', '{genres}' => 'Жанры', '{year}' => 'Год издания'], $settingsVariables))
                                    ->required(),
                                SeoTemplateInput::make('seo_desc_book')
                                    ->label('Мета-описание')
                                    ->multiline()
                                    ->variables(array_merge(['{title}' => 'Название книги', '{author}' => 'Автор', '{genres}' => 'Жанры', '{year}' => 'Год издания'], $settingsVariables))
                                    ->required(),
                            ]),
                        Tabs\Tab::make('Авторы')
                            ->schema([
                                SeoTemplateInput::make('seo_title_author')
                                    ->label('SEO-заголовок')
                                    ->variables(array_merge(['{name}' => 'Имя автора'], $settingsVariables))
                                    ->required(),
                                SeoTemplateInput::make('seo_desc_author')
                                    ->label('Мета-описание')
                                    ->multiline()
                                    ->variables(array_merge(['{name}' => 'Имя автора'], $settingsVariables))
                                    ->required(),
                            ]),
                        Tabs\Tab::make('Жанры')
                            ->schema([
                                SeoTemplateInput::make('seo_title_genre')
                                    ->label('SEO-заголовок')
                                    ->variables(array_merge(['{name}' => 'Название жанра'], $settingsVariables))
                                    ->required(),
                                SeoTemplateInput::make('seo_desc_genre')
                                    ->label('Мета-описание')
                                    ->multiline()
                                    ->variables(array_merge(['{name}' => 'Название жанра'], $settingsVariables))
                                    ->required(),
                            ]),
                        Tabs\Tab::make('Серии')
                            ->schema([
                                SeoTemplateInput::make('seo_title_series')
                                    ->label('SEO-заголовок')
                                    ->variables(array_merge(['{name}' => 'Название серии'], $settingsVariables))
                                    ->required(),
                                SeoTemplateInput::make('seo_desc_series')
                                    ->label('Мета-описание')
                                    ->multiline()
                                    ->variables(array_merge(['{name}' => 'Название серии'], $settingsVariables))
                                    ->required(),
                            ]),
                    ])
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Сохранить настройки')
                ->submit('save')
                ->color('primary'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => 'tpl_' . $key],
                ['value' => $value]
            );
        }

        Notification::make()
            ->title('Настройки SEO успешно сохранены')
            ->success()
            ->send();
    }
}
