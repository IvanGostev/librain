<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryEntry extends Model
{
    protected $fillable = ['user_id', 'book_id', 'status', 'progress_percent', 'is_favorite'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
