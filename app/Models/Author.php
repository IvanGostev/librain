<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory, \App\Traits\HandleImageUploads;

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->isDirty('photo') && $model->photo) {
                if (!str_ends_with($model->photo, '.webp')) {
                    $model->photo = $model->convertToWebp($model->photo);
                }
            }
        });
    }

    protected $fillable = ['user_id', 'name', 'slug', 'bio', 'photo', 'views_count'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function books()
    {
        return $this->belongsToMany(Book::class);
    }
}
