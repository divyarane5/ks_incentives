<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'city', 'locality', 'status', 'created_by'];

    // Automatically set 'name' when city or locality is set
    public function setCityAttribute($value)
    {
        $this->attributes['city'] = $value;
        $this->updateName();
    }

    public function setLocalityAttribute($value)
    {
        $this->attributes['locality'] = $value;
        $this->updateName();
    }

    protected function updateName()
    {
        $city = $this->attributes['city'] ?? '';
        $locality = $this->attributes['locality'] ?? '';
        $this->attributes['name'] = trim($city . ' - ' . $locality);
    }
}
