<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory, \App\Traits\HandleImageUploads;

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->isDirty('cover_image') && $model->cover_image) {
                if (!str_ends_with($model->cover_image, '.webp')) {
                    $model->cover_image = $model->convertToWebp($model->cover_image);
                }
            }
        });
    }

    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'description',
        'cover_image',
        'status',
        'age_rating',
        'views',
        'rating',
        'is_published',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'file_txt',
        'file_fb2',
        'file_epub',
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'book_genre');
    }

    public function series()
    {
        return $this->belongsToMany(Series::class, 'book_series')->withPivot('order');
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class)->orderBy('order');
    }

    public function dailyViews()
    {
        return $this->hasMany(\App\Models\BookDailyView::class);
    }

    public function libraryEntries()
    {
        return $this->hasMany(LibraryEntry::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
