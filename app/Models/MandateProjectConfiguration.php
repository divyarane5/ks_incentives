<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MandateProjectConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'mandate_project_id',
        'config',
        'carpet_area',
    ];

    public function project()
    {
        return $this->belongsTo(MandateProject::class);
    }
}
