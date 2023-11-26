<?php

// database/migrations/xxxx_xx_xx_create_duels_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDuelsTable extends Migration {
    public function up() {
        Schema::create('duels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('league_id');
            $table->unsignedBigInteger('winner_user_id');
            $table->integer('winner_score');
            $table->decimal('winner_score_value', 10, 2);
            $table->unsignedBigInteger('loser_user_id');
            $table->integer('loser_score');
            $table->decimal('loser_score_value', 10, 2);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('league_id')->references('id')->on('leagues');
            $table->foreign('winner_user_id')->references('id')->on('users');
            $table->foreign('loser_user_id')->references('id')->on('users');
        });
    }

    public function down() {
        Schema::dropIfExists('duels');
    }
}
