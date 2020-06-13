<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function booted()
    {
        static::updating(function (self $user) {
            if ($user->getKey() === 1) {
                // We update the tenant's email when the admin user's email is updated
                // so that the tenant can find his account even after email change.
                Tenant::where('email', $user->getOriginal('email'))
                ->update($user->only(['email']));
            }
        });
    }

    public function getGravatarUrlAttribute()
    {
        return "https://www.gravatar.com/avatar/" . md5(strtolower(trim($this->email)));
    }
}
