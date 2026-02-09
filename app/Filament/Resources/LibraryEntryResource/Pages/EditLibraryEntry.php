<?php

namespace App\Filament\Resources\LibraryEntryResource\Pages;

use App\Filament\Resources\LibraryEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLibraryEntry extends EditRecord
{
    protected static string $resource = LibraryEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
