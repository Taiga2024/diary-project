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
        Schema::create('followers', function (Blueprint $table) {
            $table->foreignId('following_id')->constrained('users')->cascadeOnDelete()->comment('フォローしているユーザーID');
            $table->foreignId('followed_id')->constrained('users')->cascadeOnDelete()->comment('フォローされているユーザーID');
            $table->timestamps();
            
            $table->primary(['following_id', 'followed_id']);
            $table->unique(['following_id', 'followed_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followers');
    }
};
