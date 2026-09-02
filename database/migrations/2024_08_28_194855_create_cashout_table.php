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
        Schema::create('cashout_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('category')->nullable();
            $table->string('bg_color')->nullable();
            $table->integer('fee')->default(0);
            $table->integer('minimum')->default(1000);
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('payment_title')->nullable();
            $table->string('payment_note')->nullable();
            $table->timestamps();
        });


        Schema::create('cashout_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cashout_method_id')->constrained()->onDelete('cascade');
            $table->integer('price');
            $table->timestamps();
        });

        Schema::create('cashout_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('method_name');
            $table->string('method_image');
            $table->float('amount');
            $table->string('address');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashout_prices');
        Schema::dropIfExists('cashout_methods');
        Schema::dropIfExists('cashout_requests');
    }
};
