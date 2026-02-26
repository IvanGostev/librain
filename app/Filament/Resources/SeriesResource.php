<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeriesResource\Pages;
use App\Filament\Resources\SeriesResource\RelationManagers;
use App\Models\Series;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SeriesResource extends Resource
{
    protected static ?string $model = Series::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationLabel = 'Серии';

    protected static ?string $pluralModelLabel = 'Серии';

    protected static ?string $modelLabel = 'Серия';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Forms\Components\Section::make('Информация о серии')
                        ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Название')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null),
                                Forms\Components\TextInput::make('slug')
                                    ->label('Слаг')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Series::class, 'slug', ignoreRecord: true),
                                Forms\Components\Textarea::make('description')
                                    ->label('Описание')
                                    ->columnSpanFull(),
                            ])->columns(2),

                    Forms\Components\Section::make('Медиа')
                        ->schema([
                                Forms\Components\FileUpload::make('cover')
                                    ->label('Обложка')
                                    ->image()
                                    ->disk('public')
                                    ->directory('series'),
                            ]),

                    \App\Filament\Helpers\SeoHelper::seoSection('series'),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                    Tables\Columns\ImageColumn::make('cover')
                        ->label('Обложка'),
                    Tables\Columns\TextColumn::make('name')
                        ->label('Название')
                        ->searchable()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('slug')
                        ->label('Слаг')
                        ->searchable()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Создано')
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
            'index' => Pages\ListSeries::route('/'),
            'create' => Pages\CreateSeries::route('/create'),
            'edit' => Pages\EditSeries::route('/{record}/edit'),
        ];
    }
}
