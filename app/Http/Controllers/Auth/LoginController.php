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
        $user = $this->loginUser($request);

        // Found by employee ID or mobile: sign in that exact person (works even without an email).
        if ($user) {
            return ['id' => $user->id, 'password' => $request->password, 'status' => 'active'];
        }

        return [
            'email' => trim((string) $request->email),
            'password' => $request->password,
            'status' => 'active',
        ];
    }

    /**
     * Besides email, people can sign in with their employee ID (e.g. KRE001) or their
     * 10-digit mobile number (official or personal contact). Only Active employees.
     */
    private function loginUser(Request $request): ?User
    {
        $login = trim((string) $request->email);
        if ($login === '' || str_contains($login, '@')) {
            return null;
        }

        $byCode = User::where('status', 'Active')
            ->whereRaw('UPPER(TRIM(employee_code)) = ?', [strtoupper($login)])
            ->get(['id', 'email']);
        if ($byCode->count() === 1) {
            return $byCode->first();
        }

        $digits = substr(preg_replace('/\D+/', '', $login), -10);
        if (strlen($digits) !== 10) {
            return null;
        }

        return User::where('status', 'Active')
            ->where(fn ($q) => $q->where('official_contact', 'like', '%'.$digits)->orWhere('personal_contact', 'like', '%'.$digits))
            ->get(['id', 'email'])->first();
    }

    /** Email, employee ID or a 10-digit mobile number. */
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
        // Right password but not Active (Exited / Inactive): say so plainly. Wrong password: the usual message.
        $blocked = \App\Support\AccountAccess::find((string) $request->email)->take(5)
            ->first(fn ($u) => ! \App\Support\AccountAccess::canSignIn($u)
                && $u->password && \Illuminate\Support\Facades\Hash::check((string) $request->password, $u->password));

        if ($blocked) {
            throw ValidationException::withMessages([
                'email' => [\App\Support\AccountAccess::blockedMessage($blocked)],
            ]);
        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }
}