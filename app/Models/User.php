<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Session;
use App\Models\Main\Account;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $connection = 'main';

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            throw new Exception('No se puede crear este modelo directamente');
        });

        static::deleting(function ($model) {
            throw new Exception('No se puede eliminar este modelo directamente');
        });
    }

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
     *
     * @return string
     */
    public function getNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function adminlte_desc()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function realUser()
    {
        if (! Session::has('current_real_user_auth')) {
            $realUserId = Session::get('real_userid');
            if ($realUserId > 0) {
                $user = User::with('role')->find($realUserId);
                Session::flash('current_real_user_auth',$user);
                return $user;
            }
            Session::flash('current_real_user_auth', $this);
            return $this;
        }else{
            return Session::get('current_real_user_auth');
        }
    }
    public function account(){
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Main\UserRole');
    }
}
