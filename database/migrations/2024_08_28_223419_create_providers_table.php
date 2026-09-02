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
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['offer', 'survey']);
            $table->string('image')->nullable();
            $table->string('bg_color')->nullable();
            $table->string('badge')->nullable();
            $table->string('badge_bg_color')->nullable();
            $table->boolean('show_rate')->nullable();
            $table->integer('rate')->nullable();
            $table->text('url')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_mobile')->default(true);
            $table->json('sdk_data')->nullable();
            $table->integer('unlock_level')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('providers');
    }
};
