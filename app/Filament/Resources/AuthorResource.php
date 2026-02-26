<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuthorResource\Pages;
use App\Filament\Resources\AuthorResource\RelationManagers;
use App\Models\Author;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AuthorResource extends Resource
{
    protected static ?string $model = Author::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Авторы';

    protected static ?string $pluralModelLabel = 'Авторы';

    protected static ?string $modelLabel = 'Автор';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Forms\Components\Section::make('Информация об авторе')
                        ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label('Пользователь')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\TextInput::make('name')
                                    ->label('Имя/Псевдоним')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null),
                                Forms\Components\TextInput::make('slug')
                                    ->label('Слаг')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Author::class, 'slug', ignoreRecord: true),
                                Forms\Components\Textarea::make('bio')
                                    ->label('Биография')
                                    ->columnSpanFull(),
                            ])->columns(2),

                    Forms\Components\Section::make('Параметры')
                        ->schema([
                                Forms\Components\FileUpload::make('photo')
                                    ->label('Фото')
                                    ->image()
                                    ->disk('public')
                                    ->directory('authors'),
                                Forms\Components\TextInput::make('views_count')
                                    ->label('Просмотры профиля')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                            ])->columns(2),

                    \App\Filament\Helpers\SeoHelper::seoSection('author'),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                    Tables\Columns\ImageColumn::make('photo')
                        ->label('Фото')
                        ->circular(),
                    Tables\Columns\TextColumn::make('name')
                        ->label('Имя')
                        ->searchable()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('user.name')
                        ->label('Аккаунт')
                        ->placeholder('Не привязан')
                        ->sortable(),
                    Tables\Columns\TextColumn::make('views_count')
                        ->label('Просмотры')
                        ->numeric()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Добавлен')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ])
            ->filters([

                ])
            ->actions([
                    Tables\Actions\EditAction::make(),
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

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuthors::route('/'),
            'create' => Pages\CreateAuthor::route('/create'),
            'edit' => Pages\EditAuthor::route('/{record}/edit'),
        ];
    }
}
