<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Пользователи';

    protected static ?string $pluralModelLabel = 'Пользователи';

    protected static ?string $modelLabel = 'Пользователь';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Forms\Components\Section::make('Данные пользователя')
                        ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Имя')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('username')
                                    ->label('Никнейм')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('role')
                                    ->label('Роль')
                                    ->options([
                                            'user' => 'Пользователь',
                                            'admin' => 'Администратор',
                                        ])
                                    ->required(),
                                Forms\Components\Toggle::make('is_blocked')
                                    ->label('Заблокирован')
                                    ->default(false),
                            ])->columns(2),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                    Tables\Columns\TextColumn::make('name')
                        ->label('Имя')
                        ->searchable()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('username')
                        ->label('Никнейм')
                        ->searchable()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('email')
                        ->label('Email')
                        ->searchable()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('role')
                        ->label('Роль')
                        ->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'admin' => 'danger',
                            'user' => 'success',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn(string $state): string => match ($state) {
                            'admin' => 'Админ',
                            'user' => 'Пользователь',
                            default => $state,
                        }),
                    Tables\Columns\IconColumn::make('is_blocked')
                        ->label('Блок')
                        ->boolean()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Зарегистрирован')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ])
            ->filters([
                    Tables\Filters\SelectFilter::make('role')
                        ->label('Роль')
                        ->options([
                                'user' => 'Пользователь',
                                'admin' => 'Администратор',
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
