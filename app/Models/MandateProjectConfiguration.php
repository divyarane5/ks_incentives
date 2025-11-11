<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class MandateProjectConfiguration extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'mandate_project_id',
        'config',
        'carpet_area',
    ];

    public function project()
    {
        return $this->belongsTo(MandateProject::class, 'mandate_project_id');
    }

    protected static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = is_object(Auth::user()) ? Auth::user()->id : 1;
        });
    }
}
