<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndentApproveLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'indent_id',
        'user_id',
        'status',
        'description',
        'submission_date',
    ];
}
