<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBook extends CreateRecord
{
    protected static string $resource = BookResource::class;
    protected function afterCreate(): void
    {
        /** @var \App\Models\Book $book */
        $book = $this->record;


        $book->load('chapters');

        if ($book->chapters->isNotEmpty()) {
            $service = app(\App\Services\BookImportService::class);
            $updated = false;


            if (empty($book->file_fb2)) {
                $filename = 'books/files/' . $book->slug . '.fb2';
                $service->generateFb2($book, $book->chapters, $filename);
                $book->file_fb2 = $filename;
                $updated = true;
            }


            if (empty($book->file_txt)) {
                $filename = 'books/files/' . $book->slug . '.txt';
                $content = $service->generateTxtContent($book, $book->chapters);
                \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $content);
                $book->file_txt = $filename;
                $updated = true;
            }


            if (empty($book->file_epub)) {
                $filename = 'books/files/' . $book->slug . '.epub';
                if ($service->generateEpub($book, $book->chapters, $filename)) {
                    $book->file_epub = $filename;
                    $updated = true;
                }
            }

            if ($updated) {

                $book->save();
            }
        }
    }
}
