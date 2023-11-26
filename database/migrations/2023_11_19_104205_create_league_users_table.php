<?php

// database/migrations/xxxx_xx_xx_create_league_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeagueUsersTable extends Migration {
    public function up() {
        Schema::create('league_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('league_id');
            $table->integer('elo');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('league_id')->references('id')->on('leagues');
        });
    }

    public function down() {
        Schema::dropIfExists('league_users');
    }
}


// exemple of data to insert in database
// {
//     "user_id": 1,
//     "league_id": 1,
//     "elo": 1000
// }
