<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'phone_verified_at' => 'datetime',
    ];

    public function getJWTCustomClaims(): array
    {
        return [
            'phone' => $this->phone,
        ];
    }

    public function master(): HasOne
    {
        return $this->hasOne(Master::class);
    }

    public function client(): HasOne
    {
        return $this->hasOne(Client::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function fcmToken(): HasOne
    {
        return $this->hasOne(FcmToken::class);
    }

}
