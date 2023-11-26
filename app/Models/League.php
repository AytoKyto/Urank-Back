<?php

// app/Models/League.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class League extends Model {
    use HasFactory;

    protected $table = 'leagues';
    protected $fillable = ['icon', 'name', 'admin_user_id'];

    public function adminUser() {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function leagueUsers() {
        return $this->hasMany(LeagueUser::class, 'league_id');
    }

    public function duels() {
        return $this->hasMany(Duel::class, 'league_id');
    }
}
