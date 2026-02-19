<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'book_id', 'rating', 'comment', 'parent_id', 'is_approved', 'guest_name'];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($review) {
            $review->updateBookRating();
        });

        static::deleted(function ($review) {
            $review->updateBookRating();
        });
    }

    public function updateBookRating()
    {
        if ($this->book_id) {
            $book = $this->book;
            $average = $book->reviews()
                ->where('is_approved', true)
                ->whereNull('parent_id')
                ->whereNotNull('rating')
                ->avg('rating');
            $book->update(['rating' => round($average, 1) ?: 0]);
        }
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function parent()
    {
        return $this->belongsTo(Review::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Review::class, 'parent_id');
    }

    public function votes()
    {
        return $this->hasMany(ReviewVote::class);
    }

    public function likes()
    {
        return $this->votes()->where('type', 'like');
    }

    public function dislikes()
    {
        return $this->votes()->where('type', 'dislike');
    }
}
