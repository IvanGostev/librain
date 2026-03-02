<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Series extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'description', 'cover'];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_series')
            ->withPivot('order')
            ->orderByRaw('CASE WHEN book_series.order > 0 THEN 0 ELSE 1 END')
            ->orderBy('book_series.order', 'asc')
            ->orderBy('books.created_at', 'desc');
    }
}
