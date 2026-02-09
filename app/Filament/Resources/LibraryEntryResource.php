<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LibraryEntryResource\Pages;
use App\Filament\Resources\LibraryEntryResource\RelationManagers;
use App\Models\LibraryEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LibraryEntryResource extends Resource
{
    protected static ?string $model = LibraryEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';

    protected static ?string $navigationLabel = 'Библиотека пользователей';

    protected static ?string $pluralModelLabel = 'Записи библиотек';

    protected static ?string $modelLabel = 'Запись библиотеки';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Forms\Components\Section::make('Детали записи')
                        ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label('Пользователь')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\Select::make('book_id')
                                    ->label('Книга')
                                    ->relationship('book', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\Select::make('status')
                                    ->label('Статус')
                                    ->options([
                                            'planned' => 'Буду читать',
                                            'reading' => 'Читаю',
                                            'dropped' => 'Брошено',
                                            'finished' => 'Прочитано',
                                        ])
                                    ->required(),
                                Forms\Components\TextInput::make('progress_percent')
                                    ->label('Процент прогресса')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(100),
                                Forms\Components\Toggle::make('is_favorite')
                                    ->label('В избранном')
                                    ->required(),
                            ])->columns(2),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                    Tables\Columns\TextColumn::make('user.name')
                        ->label('Пользователь')
                        ->sortable()
                        ->searchable(),
                    Tables\Columns\TextColumn::make('book.title')
                        ->label('Книга')
                        ->sortable()
                        ->searchable(),
                    Tables\Columns\TextColumn::make('status')
                        ->label('Статус')
                        ->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'planned' => 'gray',
                            'reading' => 'primary',
                            'finished' => 'success',
                            'dropped' => 'danger',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn(string $state): string => match ($state) {
                            'planned' => 'Буду читать',
                            'reading' => 'Читаю',
                            'dropped' => 'Брошено',
                            'finished' => 'Прочитано',
                            default => $state,
                        }),
                    Tables\Columns\TextColumn::make('progress_percent')
                        ->label('Прогресс')
                        ->suffix('%')
                        ->sortable(),
                    Tables\Columns\IconColumn::make('is_favorite')
                        ->label('Избр.')
                        ->boolean(),
                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Дата')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ])
            ->filters([
                    Tables\Filters\SelectFilter::make('status')
                        ->label('Статус')
                        ->options([
                                'planned' => 'Буду читать',
                                'reading' => 'Читаю',
                                'dropped' => 'Брошено',
                                'finished' => 'Прочитано',
                            ]),
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
            'index' => Pages\ListLibraryEntries::route('/'),
            'create' => Pages\CreateLibraryEntry::route('/create'),
            'edit' => Pages\EditLibraryEntry::route('/{record}/edit'),
        ];
    }
}
