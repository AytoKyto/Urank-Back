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
        Schema::create('invitations_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // L'utilisateur qui a envoyé l'invitation
            $table->unsignedBigInteger('invited_user_id')->nullable(); // L'utilisateur invité (peut être nul si l'invitation n'a pas encore été acceptée)
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('invited_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations_user');
    }
};
