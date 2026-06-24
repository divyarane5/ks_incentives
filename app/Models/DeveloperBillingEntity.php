<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeveloperBillingEntity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'developer_id',
        'entity_name',
        'gstin',
        'status'
    ];

    public function developer()
    {
        return $this->belongsTo(Developer::class);
    }
}