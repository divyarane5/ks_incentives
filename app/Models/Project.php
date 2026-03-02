<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;


class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'developer_id',
         'base_brokerage_percent',
         'rera_number',
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

    public function ladders()
    {
        return $this->hasMany(ProjectLadder::class);
    }
}
