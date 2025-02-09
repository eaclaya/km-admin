<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

use App\Events\UserLoggedIn;
use App\Models\User;

use Carbon\Carbon;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'account' => ['required', 'integer'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();
        $data = $this->request->all();

        $user = User::where('email', '=', $data['email'])->first();
        if ($user == null) {
            Session::flash('error', trans('texts.invalid_credentials'));
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        if ($user->allowed_account_ids > 0) {
            $allowedAccounts = explode(',', $user->allowed_account_ids);
            if (in_array($this->request->get('account'), $allowedAccounts) == false) {
                throw ValidationException::withMessages([
                    'account' => 'No tiene permiso para acceder a esta Tienda',
                ]);
            }
        }

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }else{
            Session::put('authentication_token', null);
            Session::put('real_useraccount', $user->account_id);
            Session::put('real_userid', $user->id);
            Cache::forever('real_userid', $user->id);

            if ($user->is_superuser && $user->id != 81) {
                $newUser = User::where('account_id', $data['account'])->where('is_superuser', 1)->first();
                if ($newUser) {
                    Auth::login($newUser);
                }
            }
        }

        // dd('aqui');

        /* $userId = Auth::id();

        $codes = DB::connection('main')
            ->table('users')
            ->join('user_permissions', 'users.role_id', '=', 'user_permissions.role_id')
            ->join('user_resources', 'user_permissions.resource_id', '=', 'user_resources.id')
            ->where('users.id', $userId)
            ->pluck('user_resources.code')
            ->toArray();

        session(['user_codes' => $codes]); */
        UserLoggedIn::dispatch();

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
