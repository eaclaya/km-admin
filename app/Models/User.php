<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Main\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Session;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'main';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's full name.
     */
    public function getNameAttribute(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function adminlte_desc()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function realUser()
    {
        if (! Session::has('current_real_user_auth')) {
            $realUserId = Session::get('real_userid');
            if ($realUserId > 0) {
                $user = User::with('role')->find($realUserId);
                Session::flash('current_real_user_auth', $user);

                return $user;
            }
            Session::flash('current_real_user_auth', $this);

            return $this;
        } else {
            return Session::get('current_real_user_auth');
        }
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Main\UserRole');
    }

    public function _can($code = '')
    {
        if (trim($code) == '') {
            return false;
        }
        $permissions = Session::get('user_permissions');
        $permissions = $permissions ? $permissions : [];

        return isset($permissions[$code]) ? true : false;
        //        return in_array($code, $permissions);
    }
}
