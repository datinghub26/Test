<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bonuses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->integer('reward_points');
            $table->timestamp('expires_at');
            $table->boolean('is_active')->default(true);
            $table->integer('max_uses')->default(1);
            $table->timestamps();
        });

        Schema::create('bonus_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bonus_id')->constrained('bonuses')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bonuses');
        Schema::dropIfExists('bonus_usages');
    }
};
