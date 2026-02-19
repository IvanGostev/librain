<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewVote extends Model
{
    protected $fillable = ['review_id', 'user_id', 'ip_address', 'type'];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }
}
