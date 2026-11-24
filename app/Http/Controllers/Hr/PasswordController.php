<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Support\AccountAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/** KeyMe > Change password (everyone). Also where people on the starting password are sent. */
class PasswordController extends Controller
{
    public function edit(Request $request)
    {
        return view('hr.password', [
            'mustChange' => (bool) $request->session()->get('pw_default'),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => AccountAccess::newPasswordRules(),
        ], AccountAccess::newPasswordMessages());

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Your current password is not correct.']);
        }
        if (Hash::check($request->new_password, $user->password)) {
            return back()->withErrors(['new_password' => 'The new password must be different from your current one.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        $request->session()->put('pw_default', false);

        return redirect()->route('me.password')->with('success', 'Your password has been changed. Use the new one next time you sign in.');
    }
}
