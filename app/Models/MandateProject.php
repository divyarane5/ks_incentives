<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MandateProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_name',
        'brand_name',
        'location',
        'rera_number',
        'property_type',
    ];

    public function configurations()
    {
        return $this->hasMany(MandateProjectConfiguration::class);
    }
}
