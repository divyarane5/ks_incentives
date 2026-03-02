<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class DeveloperLadder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'developer_ladders';

    protected $fillable = [
         'developer_id',
        'aop',
        'min_aop',
        'max_aop',
        'ladder',
        'ladder_type',
        'aop_s_date',
        'aop_e_date',
        'status',
        'created_by',
    ];
    protected static function boot() { 
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = is_object(Auth::user()) ? Auth::user()->id : 1;
        });
    }
    public function developer()
    {
        return $this->belongsTo(Developer::class);
    }
}
