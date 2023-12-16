<?php

// database/migrations/xxxx_xx_xx_create_leagues_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaguesTable extends Migration {
    public function up() {
        Schema::create('leagues', function (Blueprint $table) {
            $table->id();
            $table->string('icon');
            $table->string('name');
            $table->unsignedBigInteger('admin_user_id');
            $table->timestamps();

            $table->foreign('admin_user_id')->references('id')->on('users');
        });
    }

    public function down() {
        Schema::dropIfExists('leagues');
    }
}


// exemple of data to insert in database
// {
//     "icon": "🏀",
//     "name": "League 1",
//     "admin_user_id": 1
// }
