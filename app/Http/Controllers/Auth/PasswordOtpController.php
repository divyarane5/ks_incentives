<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordOtpMail;
use App\Models\User;
use App\Support\AccountAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Forgot password: a 6-digit code goes to the employee's official email, then they set a new password.
 * Only Active employees. Codes last 10 minutes, allow 5 tries, and are stored hashed in the cache.
 */
class PasswordOtpController extends Controller
{
    private const MINUTES = 10;
    private const MAX_TRIES = 5;
    private const SENDS_PER_USER = 3;      // per 30 minutes
    private const SENDS_PER_IP = 10;       // per hour
    private const RESEND_GAP = 60;         // seconds

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function request()
    {
        return view('auth.otp.request');
    }

    public function send(Request $request)
    {
        $request->validate(['login' => 'required|string|max:100'], ['login.required' => 'Enter your employee ID, email or mobile number.']);

        $ipKey = 'pw-otp-ip:'.$request->ip();
        if (RateLimiter::tooManyAttempts($ipKey, self::SENDS_PER_IP)) {
            return back()->withInput()->withErrors(['login' => 'Too many requests. Please try again in an hour.']);
        }
        RateLimiter::hit($ipKey, 3600);

        $login = trim((string) $request->login);
        $user = AccountAccess::findActive($login);

        if (! $user) {
            $other = AccountAccess::find($login)->first();
            $msg = $other && ! AccountAccess::canSignIn($other)
                ? AccountAccess::blockedMessage($other)
                : "We couldn't find an active employee with that employee ID, email or mobile.";

            return back()->withInput()->withErrors(['login' => $msg]);
        }

        $email = AccountAccess::otpEmail($user);
        if (! $email) {
            return back()->withInput()->withErrors(['login' => 'There is no official email on your profile. Please ask HR to add it, or to reset your password.']);
        }

        return $this->sendCode($request, $user, $email);
    }

    public function resend(Request $request)
    {
        $user = $this->pendingUser($request);
        if (! $user || ! ($email = AccountAccess::otpEmail($user))) {
            return redirect()->route('password.request');
        }

        return $this->sendCode($request, $user, $email);
    }

    public function form(Request $request)
    {
        $user = $this->pendingUser($request);
        if (! $user) {
            return redirect()->route('password.request');
        }

        return view('auth.otp.verify', ['maskedEmail' => AccountAccess::maskEmail((string) AccountAccess::otpEmail($user))]);
    }

    public function verify(Request $request)
    {
        $user = $this->pendingUser($request);
        if (! $user) {
            return redirect()->route('password.request')->withErrors(['login' => 'Please ask for a new code.']);
        }

        $request->validate([
            'code' => ['required', 'digits:6'],
            'new_password' => AccountAccess::newPasswordRules(),
        ], ['code.required' => 'Enter the 6-digit code.', 'code.digits' => 'The code has 6 digits.'] + AccountAccess::newPasswordMessages());

        $key = $this->codeKey($user);
        $saved = Cache::get($key);
        if (! $saved || now()->timestamp > $saved['expires']) {
            Cache::forget($key);

            return back()->withErrors(['code' => 'This code has expired. Tap "Send a new code".']);
        }

        if (! Hash::check($request->code, $saved['hash'])) {
            $saved['tries']++;
            if ($saved['tries'] >= self::MAX_TRIES) {
                Cache::forget($key);

                return back()->withErrors(['code' => 'Wrong code too many times. Tap "Send a new code".']);
            }
            Cache::put($key, $saved, now()->addMinutes(self::MINUTES));
            $left = self::MAX_TRIES - $saved['tries'];

            return back()->withErrors(['code' => "That code is not right. {$left} ".($left === 1 ? 'try' : 'tries').' left.']);
        }

        Cache::forget($key);
        RateLimiter::clear($this->sendKey($user));

        $user->password = Hash::make($request->new_password);
        $user->setRememberToken(\Illuminate\Support\Str::random(60)); // ends "remember me" on other devices
        $user->save();

        $request->session()->forget('pw_reset_user');
        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('pw_default', false);

        $home = $user->usesCrm() && ! $user->isAdmin() ? route('crm.dashboard') : route('dashboard');

        return redirect($home)->with('success', 'Your new password is set. You are signed in.')->with('status', 'Your new password is set. You are signed in.');
    }

    private function sendCode(Request $request, User $user, string $email)
    {
        $sendKey = $this->sendKey($user);
        $gapKey = 'pw-otp-gap:'.$user->id;

        if (RateLimiter::tooManyAttempts($gapKey, 1)) {
            $wait = RateLimiter::availableIn($gapKey);
            $request->session()->put('pw_reset_user', $user->id);

            return redirect()->route('password.otp')->withErrors(['code' => "A code was just sent. You can ask for another in {$wait} seconds."]);
        }
        if (RateLimiter::tooManyAttempts($sendKey, self::SENDS_PER_USER)) {
            $mins = (int) ceil(RateLimiter::availableIn($sendKey) / 60);

            return redirect()->route('password.request')->withInput(['login' => $request->input('login')])
                ->withErrors(['login' => "Too many codes asked for. Please try again in {$mins} minutes."]);
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        try {
            Mail::to($email)->send(new PasswordOtpMail(explode(' ', trim((string) $user->name))[0] ?: 'there', $code, self::MINUTES));
        } catch (\Throwable $e) {
            Log::error('Password OTP email failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);

            return back()->withInput()->withErrors(['login' => "We couldn't send the email right now. Please try again in a few minutes, or ask HR to reset your password."]);
        }

        RateLimiter::hit($sendKey, 1800);
        RateLimiter::hit($gapKey, self::RESEND_GAP);
        Cache::put($this->codeKey($user), [
            'hash' => Hash::make($code),
            'tries' => 0,
            'expires' => now()->addMinutes(self::MINUTES)->timestamp,
        ], now()->addMinutes(self::MINUTES));

        $request->session()->put('pw_reset_user', $user->id);

        return redirect()->route('password.otp')->with('status', 'We sent a 6-digit code to '.AccountAccess::maskEmail($email).'.');
    }

    private function pendingUser(Request $request): ?User
    {
        $user = User::find($request->session()->get('pw_reset_user'));

        return AccountAccess::canSignIn($user) ? $user : null;
    }

    private function codeKey(User $user): string
    {
        return 'pw-otp:'.$user->id;
    }

    private function sendKey(User $user): string
    {
        return 'pw-otp-send:'.$user->id;
    }
}
