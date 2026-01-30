<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MandateProjectLadder extends Model
{
    use SoftDeletes;

    protected $table = 'mandate_project_ladders';

    protected $fillable = [
        'mandate_project_id',
        'timeline_from',
        'timeline_to',
        'no_of_units',
        'payout_percentage',
        'status',
        'created_by',
    ];

    protected $casts = [
        'timeline_from' => 'date',
        'timeline_to'   => 'date',
    ];

    /**
     * Child → Parent
     * Each ladder belongs to ONE project
     */
    public function project()
    {
        return $this->belongsTo(MandateProject::class, 'mandate_project_id');
    }
}
