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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type')->default('activity');
            $table->string('name');
            $table->string('offer_id', 100)->nullable()->index();
            $table->string('offer_name')->nullable();
            $table->string('offer_trx_id')->nullable();
            $table->text('image')->nullable();
            $table->float('points');
            $table->float('payout');
            $table->string('ip', 45)->nullable();
            $table->string('country_code', 2)->nullable();
            $table->enum('status', ['approved', 'pending', 'rejected'])->default('approved');
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
