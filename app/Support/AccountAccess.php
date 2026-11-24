<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

/**
 * Who may sign in, and the starting password rules.
 * Only employees whose status is Active can sign in (Inactive and Exited cannot).
 */
class AccountAccess
{
    /** Password every new employee gets; they must change it after their first sign-in. */
    public const DEFAULT_PASSWORD = 'Welcome@123';

    public static function canSignIn(?User $user): bool
    {
        return $user && strcasecmp(trim((string) $user->status), 'Active') === 0;
    }

    public static function usesDefaultPassword(User $user): bool
    {
        return $user->password && Hash::check(self::DEFAULT_PASSWORD, $user->password);
    }

    /** Message shown to someone who is not Active. */
    public static function blockedMessage(?User $user): string
    {
        $status = trim((string) $user?->status) ?: 'not active';

        return "Your account is {$status}, so you can't sign in. Please contact HR if this is wrong.";
    }

    /**
     * Everyone (any status) matching an email, employee ID or 10-digit mobile.
     */
    public static function find(string $login): Collection
    {
        $login = trim($login);
        if ($login === '') {
            return collect();
        }

        if (str_contains($login, '@')) {
            return User::where('email', $login)->orWhere('official_email', $login)->get();
        }

        $byCode = User::whereRaw('UPPER(TRIM(employee_code)) = ?', [strtoupper($login)])->get();
        if ($byCode->isNotEmpty()) {
            return $byCode;
        }

        $digits = substr(preg_replace('/\D+/', '', $login), -10);
        if (strlen($digits) !== 10) {
            return collect();
        }

        return User::where(fn ($q) => $q->where('official_contact', 'like', '%'.$digits)->orWhere('personal_contact', 'like', '%'.$digits))->get();
    }

    /** The one Active employee for this login, if exactly one matches. */
    public static function findActive(string $login): ?User
    {
        $active = self::find($login)->filter(fn ($u) => self::canSignIn($u))->values();

        return $active->count() === 1 ? $active->first() : null;
    }

    /** Where OTPs go: the official email, else the login email. */
    public static function otpEmail(User $user): ?string
    {
        foreach ([$user->official_email, $user->email] as $email) {
            $email = trim((string) $email);
            if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $email;
            }
        }

        return null;
    }

    /** s*****h@keystone.com */
    public static function maskEmail(string $email): string
    {
        [$name, $domain] = explode('@', $email, 2);
        $shown = mb_strlen($name) <= 2 ? mb_substr($name, 0, 1) : mb_substr($name, 0, 1).str_repeat('*', max(1, mb_strlen($name) - 2)).mb_substr($name, -1);

        return $shown.'@'.$domain;
    }

    /** At least 8 characters with a letter and a number, and not the starting password. */
    public static function newPasswordRules(): array
    {
        return [
            'required', 'string', 'min:8', 'max:100', 'confirmed',
            'regex:/[A-Za-z]/', 'regex:/[0-9]/',
            function ($attribute, $value, $fail) {
                if (strcasecmp((string) $value, self::DEFAULT_PASSWORD) === 0) {
                    $fail('Please choose a password other than the starting password.');
                }
            },
        ];
    }

    public static function newPasswordMessages(): array
    {
        return [
            'new_password.min' => 'Use at least 8 characters.',
            'new_password.regex' => 'Use at least one letter and one number.',
            'new_password.confirmed' => 'The two new passwords do not match.',
        ];
    }
}
