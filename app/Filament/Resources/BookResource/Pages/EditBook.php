<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBook extends EditRecord
{
    protected static string $resource = BookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        /** @var \App\Models\Book $book */
        $book = $this->record;

        $book->load('chapters');

        if ($book->chapters->isNotEmpty() || !empty($book->full_text)) {
            $service = app(\App\Services\BookImportService::class);
            $updated = false;

            $chaptersOrText = $book->chapters->isNotEmpty() 
                ? $book->chapters 
                : collect([new \App\Models\Chapter(['title' => $book->title, 'content' => strip_tags(str_replace(['<br>', '<p>', '</p>'], ["\n", "", "\n\n"], $book->full_text))])]);


            if (empty($book->file_fb2)) {
                $filename = 'books/files/' . $book->slug . '.fb2';
                $service->generateFb2($book, $chaptersOrText, $filename);
                $book->file_fb2 = $filename;
                $updated = true;
            }


            if (empty($book->file_txt)) {
                $filename = 'books/files/' . $book->slug . '.txt';
                $content = $service->generateTxtContent($book, $chaptersOrText);
                \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $content);
                $book->file_txt = $filename;
                $updated = true;
            }


            if (empty($book->file_epub)) {
                $filename = 'books/files/' . $book->slug . '.epub';
                if ($service->generateEpub($book, $chaptersOrText, $filename)) {
                    $book->file_epub = $filename;
                    $updated = true;
                }
            }

            if ($updated) {
                $book->saveQuietly();
            }
        }
    }
}
