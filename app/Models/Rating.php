<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'book_id', 'rating', 'ip_address'];

    protected static function booted()
    {
        static::saved(function ($rating) {
            $rating->book->recalculateRating();
        });

        static::deleted(function ($rating) {
            $rating->book->recalculateRating();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
