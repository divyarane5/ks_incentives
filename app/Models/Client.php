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

    public function getAllData($id)
	{
	    return DB::table('clients')
                    ->select('users.name as u_name','users.id as u_id','designations.name as d_name','clients.client_name','clients.client_email','clients.sales_person','clients.created_by','templates.content','clients.id')
                   ->join('templates', 'clients.template_id','=','templates.id')
                   ->join('users', 'clients.created_by','=','users.id')
                   ->join('designations', 'users.designation_id','=','designations.id','inner')
	               ->where('clients.id',$id)
	               ->first();
                   
	}

}
