<?php
namespace App\Repositories;

use App\Interfaces\ClientRepositoryInterface;
use App\Models\Client;

class ClientRepository implements ClientRepositoryInterface
{
    public function getClientDetails($id)
    {
        return Client::select('users.name as u_name','users.id as u_id','designations.name as d_name','clients.client_name','clients.client_email','clients.sales_person','clients.created_by','templates.content','clients.id','users.mobile','clients.subject_name')
                    ->join('templates', 'clients.template_id','=','templates.id')
                    ->join('users', 'clients.created_by','=','users.id')
                    ->leftJoin('designations', 'users.designation_id','=','designations.id','inner')
                    ->where('clients.id',$id)
                    ->first();
    }
}
