<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BookSeries extends Pivot
{
    protected $table = 'book_series';
    
    public $timestamps = false;

    protected $fillable = [
        'book_id',
        'series_id',
        'order',
    ];

    public function series()
    {
        return $this->belongsTo(Series::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
