<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class IndentComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'indent_id',
        'comment'
    ];

    protected static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = is_object(Auth::user()) ? Auth::user()->id : 1;
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
