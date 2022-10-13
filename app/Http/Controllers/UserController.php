<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $data['users'] = User::orderBy('id','desc')->paginate(5);
        return view('users.index', $data);
    }
    public function create()
    {
        return view('users.create');
    }
}
