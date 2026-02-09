<?php

namespace App\Filament\Resources\BookResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChaptersRelationManager extends RelationManager
{
    protected static string $relationship = 'chapters';

    protected static ?string $title = 'Главы';

    protected static ?string $modelLabel = 'Глава';

    protected static ?string $pluralModelLabel = 'Главы';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Заголовок главы')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('order')
                        ->label('Порядок')
                        ->numeric()
                        ->required(),
                    Forms\Components\RichEditor::make('content')
                        ->label('Текст главы')
                        ->required()
                        ->columnSpanFull(),
                ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->reorderable('order')
            ->defaultSort('order')
            ->columns([
                    Tables\Columns\TextColumn::make('order')
                        ->label('№')
                        ->sortable(),
                    Tables\Columns\TextColumn::make('title')
                        ->label('Заголовок')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Добавлена')
                        ->dateTime()
                        ->sortable(),
                ])
            ->filters([

                ])
            ->headerActions([
                    Tables\Actions\CreateAction::make()
                        ->label('Новая глава'),
                ])
            ->actions([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ->bulkActions([
                    Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\DeleteBulkAction::make(),
                    ]),
                ]);
    }
}
