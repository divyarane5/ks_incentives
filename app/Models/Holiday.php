<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = ['date', 'name', 'location_id'];

    protected $casts = ['date' => 'date'];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
