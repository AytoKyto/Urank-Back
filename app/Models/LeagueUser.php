<?php

// app/Models/LeagueUser.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeagueUser extends Model {
    use HasFactory;

    protected $table = 'league_users';
    protected $fillable = ['user_id', 'league_id', 'elo', 'type'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function league() {
        return $this->belongsTo(League::class, 'league_id');
    }
}
