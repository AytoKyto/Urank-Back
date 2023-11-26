<?php

// database/migrations/xxxx_xx_xx_create_user_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPaymentsTable extends Migration {
    public function up() {
        Schema::create('user_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['Pending', 'Completed', 'Failed']);
            $table->decimal('amount', 10, 2);
            $table->string('method', 50);
            $table->dateTime('payment_date');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down() {
        Schema::dropIfExists('user_payments');
    }
}
