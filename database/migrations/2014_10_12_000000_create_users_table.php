<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migration pour la table 'users'
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->default('1966025408@guest.test');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->default('');
            $table->bigInteger('coins')->default(0);
            $table->enum('status', ['Active', 'Inactive']);
            $table->integer('type')->default(1); // 0 = player, 1 = inviter, 2 = admin
            $table->string('avatar');
            $table->string('bg_color');
            $table->string('bg_avatar');
            $table->string('border_avatar');
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
