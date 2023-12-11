<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchUsersTable extends Migration
{
    public function up()
    {
        Schema::create('duel_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('match_id');
            $table->unsignedBigInteger('league_id');
            $table->bigInteger('league_user_elo_init');
            $table->bigInteger('league_user_elo_add');
            $table->integer('status');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('match_id')->references('id')->on('matchs');
            $table->foreign('league_id')->references('id')->on('leagues');
        });
    }

    public function down()
    {
        Schema::dropIfExists('duel_users');
    }
}
