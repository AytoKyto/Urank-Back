<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DuelUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'duel_id',
        'ranking',
        'league_id',
        'coin',
        'league_user_elo_init',
        'league_user_elo_add',
        'status'
    ];

    protected $with = ['user'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function match()
    {
        return $this->belongsTo(Duel::class);
    }

    public function league()
    {
        return $this->belongsTo(League::class);
    }
}
