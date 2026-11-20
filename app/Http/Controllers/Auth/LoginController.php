<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function credentials(Request $request)
    {
        return [
            'email' => $this->loginEmail($request),
            'password' => $request->password,
            'status' => 'active',
        ];
    }

    /**
     * Sales staff can also sign in with their mobile number (official or personal contact).
     */
    private function loginEmail(Request $request): ?string
    {
        $login = trim((string) $request->email);
        if ($login === '' || str_contains($login, '@')) {
            return $login;
        }

        $digits = substr(preg_replace('/\D+/', '', $login), -10);
        if (strlen($digits) !== 10) {
            return $login;
        }

        $user = User::where('status', 'Active')
            ->where(fn ($q) => $q->where('official_contact', 'like', '%'.$digits)->orWhere('personal_contact', 'like', '%'.$digits))
            ->get(['id', 'email'])->first();

        return $user?->email ?? $login;
    }

    /** Email or a 10-digit mobile number. */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /** Sales staff (FOS, TL, Sr. TL, CH) start on KeyLeads; everyone else on the back office. */
    protected function authenticated(Request $request, $user)
    {
        if ($user->usesCrm() && ! $user->isAdmin()) {
            return redirect()->intended(route('crm.dashboard'));
        }

        return null;
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $user = \App\Models\User::where('email', $this->loginEmail($request))->first();

        if ($user && $user->status === 'inactive') {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => ['Your account is inactive. Please contact admin.'],
            ]);
        }

        throw \Illuminate\Validation\ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }
}