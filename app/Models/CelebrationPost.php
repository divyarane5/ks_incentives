<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CelebrationPost extends Model
{
    protected $fillable = ['user_id', 'type', 'design', 'years', 'message', 'photo', 'photo_pos', 'post_date', 'posted_by'];

    protected $casts = ['post_date' => 'date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}
