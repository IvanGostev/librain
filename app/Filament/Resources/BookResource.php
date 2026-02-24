<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Filament\Resources\BookResource\RelationManagers;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Книги';

    protected static ?string $pluralModelLabel = 'Книги';

    protected static ?string $modelLabel = 'Книга';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Forms\Components\Section::make('Основная информация')
                        ->schema([
                                Forms\Components\Select::make('authors')
                                    ->label('Авторы')
                                    ->relationship('authors', 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\TextInput::make('title')
                                    ->label('Название')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null),
                                Forms\Components\TextInput::make('slug')
                                    ->label('Слаг')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Book::class, 'slug', ignoreRecord: true),
                                Forms\Components\Textarea::make('description')
                                    ->label('Описание')
                                    ->columnSpanFull(),
                            ])->columns(2),

                    Forms\Components\Section::make('Параметры и медиа')
                        ->schema([
                                Forms\Components\FileUpload::make('cover_image')
                                    ->label('Обложка')
                                    ->image()
                                    ->directory('books/covers'),
                                Forms\Components\Select::make('status')
                                    ->label('Статус')
                                    ->options([
                                            'writing' => 'В процессе',
                                            'finished' => 'Завершено',
                                        ])
                                    ->required()
                                    ->default('writing'),
                                Forms\Components\TextInput::make('age_rating')
                                    ->label('Возрастной рейтинг')
                                    ->required()
                                    ->maxLength(255)
                                    ->default('0+'),
                                Forms\Components\TextInput::make('views')
                                    ->label('Просмотры')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\Toggle::make('is_published')
                                    ->label('Опубликовано')
                                    ->required(),
                            ])->columns(2),

                    Forms\Components\Section::make('Главы')
                        ->schema([
                                Forms\Components\Repeater::make('chapters')
                                    ->relationship()
                                    ->schema([
                                            Forms\Components\TextInput::make('title')
                                                ->label('Название главы')
                                                ->required(),
                                            Forms\Components\Textarea::make('content')
                                                ->label('Текст главы')
                                                ->rows(10)
                                                ->required(),
                                            Forms\Components\TextInput::make('order')
                                                ->label('Порядок')
                                                ->numeric()
                                                ->default(1)
                                                ->required(),
                                        ])
                                    ->orderColumn('order')
                                    ->defaultItems(0)
                                    ->addActionLabel('Добавить главу')
                            ]),

                    Forms\Components\Section::make('Файлы для скачивания')
                        ->schema([
                                Forms\Components\FileUpload::make('file_txt')
                                    ->label('TXT файл')
                                    ->disk('public')
                                    ->directory('books/files')
                                    ->acceptedFileTypes(['text/plain'])
                                    ->helperText('Если не загружено, будет создано автоматически из глав.'),
                                Forms\Components\FileUpload::make('file_fb2')
                                    ->label('FB2 файл')
                                    ->disk('public')
                                    ->directory('books/files')
                                    ->acceptedFileTypes(['application/x-fictionbook+xml', 'text/xml', 'application/xml', 'application/octet-stream'])
                                    ->helperText('Если не загружено, будет создано автоматически из глав.'),
                                Forms\Components\FileUpload::make('file_epub')
                                    ->label('EPUB файл')
                                    ->disk('public')
                                    ->directory('books/files')
                                    ->acceptedFileTypes(['application/epub+zip', 'application/octet-stream'])
                                    ->helperText('Если не загружено, будет создано автоматически из глав.'),
                            ])->columns(3),

                    \App\Filament\Helpers\SeoHelper::seoSection(),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                    Tables\Columns\TextColumn::make('authors.name')
                        ->label('Авторы')
                        ->badge(),
                    Tables\Columns\TextColumn::make('title')
                        ->label('Название')
                        ->searchable(),
                    Tables\Columns\ImageColumn::make('cover_image')
                        ->label('Обложка'),
                    Tables\Columns\TextColumn::make('status')
                        ->label('Статус')
                        ->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'writing' => 'warning',
                            'finished' => 'success',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn(string $state): string => match ($state) {
                            'writing' => 'В процессе',
                            'finished' => 'Завершено',
                            default => $state,
                        }),
                    Tables\Columns\TextColumn::make('age_rating')
                        ->label('Рейтинг')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('views')
                        ->label('Просмотры')
                        ->numeric()
                        ->sortable(),
                    Tables\Columns\IconColumn::make('is_published')
                        ->label('Опубл.')
                        ->boolean(),
                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Создано')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ])
            ->filters([
                    Tables\Filters\SelectFilter::make('status')
                        ->label('Статус')
                        ->options([
                                'writing' => 'В процессе',
                                'finished' => 'Завершено',
                            ]),
                    Tables\Filters\TernaryFilter::make('is_published')
                        ->label('Опубликовано'),
                ])
            ->actions([
                    Tables\Actions\EditAction::make(),
                ])
            ->headerActions([
                    Tables\Actions\Action::make('importFb2')
                        ->label('Импорт FB2')
                        ->icon('heroicon-o-document-plus')
                        ->form([
                                Forms\Components\FileUpload::make('fb2_file')
                                    ->label('Файл FB2')
                                    ->required()
                                    ->disk('local')
                                    ->directory('temp-imports'),
                            ])
                        ->action(function (array $data, \App\Services\BookImportService $service) {
                            $filePath = $data['fb2_file'];
                            try {
                                $service->importFromFb2($filePath, 'local');
                                Storage::disk('local')->delete($filePath);
                                \Filament\Notifications\Notification::make()
                                    ->title('Успех')
                                    ->body('Книга успешно импортирована!')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Ошибка')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                ])
            ->bulkActions([
                    Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\DeleteBulkAction::make(),
                    ]),
                ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ChaptersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
