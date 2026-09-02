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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->nullable();
            $table->string('email')->unique();
            $table->enum('role', ['user', 'admin'])->default('user');
            $table->integer('avatar')->nullable();
            $table->integer('level')->default(1);
            $table->integer('exp')->default(45);
            $table->float('points')->default(0);
            $table->boolean('privacy')->default(false);
            $table->boolean('is_banned')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('users_referral_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('referral_code')->unique();
            $table->float('referral_points')->default(0);
            $table->timestamps();
        });

        Schema::create('users_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('referred_by_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('users_geo_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ip', 45)->nullable();
            $table->string('country_code', 2)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('latest_user_agent')->nullable();
            $table->string('latest_login_ip')->nullable();
            $table->timestamp('latest_login_at')->nullable();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->string('country_code', 2)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_referral_data');
        Schema::dropIfExists('users_referrals');
        Schema::dropIfExists('users_geo_data');
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
