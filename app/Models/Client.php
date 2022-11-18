<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;
use DB;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = is_object(Auth::user()) ? Auth::user()->id : 1;
        });
    }

    protected $fillable = [
        'client_name','sales_person','client_email','subject_name','template_id'
    ];


}
