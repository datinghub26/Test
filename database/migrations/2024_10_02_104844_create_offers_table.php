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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('offer_id', 100)->index();
            $table->string('provider');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('instructions')->nullable();
            $table->string('requirements')->nullable();
            $table->text('image')->nullable();
            $table->text('link')->nullable();
            $table->float('points')->default(0);
            $table->float('payout')->default(0);
            $table->json('categories')->nullable();
            $table->json('countries')->nullable();
            $table->json('devices')->nullable();
            $table->json('events')->nullable();
            $table->timestamps();

            $table->unique(['offer_id', 'provider']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
