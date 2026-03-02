<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;


class ProjectLadder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
         'project_id',
        's_booking',
        'e_booking',
        'ladder',
        'project_s_date',
        'project_e_date',
    ];
    protected $casts = [
        'project_s_date' => 'date',
        'project_e_date' => 'date',
    ];
    protected static function boot() { 
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = is_object(Auth::user()) ? Auth::user()->id : 1;
        });
    }
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
