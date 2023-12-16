<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'coins',
        'status',
        'type',
        'avatar',
        'bg_color',
        'bg_avatar',
        'border_avatar'
    ];

    public function payments() {
        return $this->hasMany(UserPayment::class, 'user_id');
    }

    public function leagues() {
        return $this->hasMany(League::class, 'admin_user_id');
    }

    public function leagueUsers() {
        return $this->hasMany(LeagueUser::class, 'user_id');
    }

    public function duels() {
        return $this->hasMany(Duel::class, 'winner_user_id');
    }

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
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
