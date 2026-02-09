<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Filament\Resources\ReviewResource\RelationManagers;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';
    protected static ?string $navigationLabel = 'Отзывы и комментарии';
    protected static ?string $modelLabel = 'Отзыв';
    protected static ?string $pluralModelLabel = 'Отзывы';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Forms\Components\Section::make('Детали отзыва')
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
                                Forms\Components\TextInput::make('rating')
                                    ->label('Оценка')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(5)
                                    ->required(),
                                Forms\Components\Textarea::make('comment')
                                    ->label('Комментарий')
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\Toggle::make('is_approved')
                                    ->label('Опубликован')
                                    ->default(false),
                            ])->columns(2),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                    Tables\Columns\TextColumn::make('user.name')
                        ->label('Пользователь')
                        ->searchable()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('book.title')
                        ->label('Книга')
                        ->searchable()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('rating')
                        ->label('Рейтинг')
                        ->sortable(),
                    Tables\Columns\TextColumn::make('comment')
                        ->label('Комментарий')
                        ->limit(50)
                        ->searchable(),
                    Tables\Columns\IconColumn::make('is_approved')
                        ->label('Опубликован')
                        ->boolean()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Дата')
                        ->dateTime()
                        ->sortable(),
                ])
            ->filters([
                    Tables\Filters\Filter::make('not_approved')
                        ->label('На модерации')
                        ->query(fn(Builder $query): Builder => $query->where('is_approved', false))
                        ->default(),
                ])
            ->actions([
                    Tables\Actions\Action::make('approve')
                        ->label('Одобрить')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->visible(fn(Review $record) => !$record->is_approved)
                        ->action(function (Review $record) {
                            $record->update(['is_approved' => true]);
                            \Illuminate\Support\Facades\Mail::to($record->user)->send(new \App\Mail\ReviewApproved($record));
                        }),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->before(function (Review $record) {
                            \Illuminate\Support\Facades\Mail::to($record->user)->send(new \App\Mail\ReviewRejected($record->book->title, $record->comment));
                        }),
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
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
