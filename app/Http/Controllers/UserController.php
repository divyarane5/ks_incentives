<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserProfileRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function account()
    {
        return view('users.account');
    }

    public function updateProfile(UserProfileRequest $request)
    {
        $user = User::find(auth()->user()->id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->dob = $request->input('dob');
        $user->gender = $request->input('gender');
        $user->save();

        //profile photo
        if ($request->has('photo')) {
            $user->photo = uploadFile($request->file('photo'), config('uploadfilepath.USER_PROFILE_PHOTO'));
            $user->save();
        }

        return redirect()->route('account')->with('success', 'Profile Updated Successfully');
    }

}
