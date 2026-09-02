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
        Schema::table('offers', function (Blueprint $table) {
            if (!Schema::hasColumn('offers', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('points');
            }
            if (!Schema::hasColumn('offers', 'is_manual')) {
                $table->boolean('is_manual')->default(false)->after('is_featured');
            }
            if (!Schema::hasColumn('offers', 'hold_duration_days')) {
                $table->integer('hold_duration_days')->default(0)->after('is_manual');
            }
        });

        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'release_at')) {
                $table->timestamp('release_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('leads', 'hold_duration_days')) {
                $table->integer('hold_duration_days')->nullable()->after('release_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'is_manual', 'hold_duration_days']);
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['release_at', 'hold_duration_days']);
        });
    }
};
