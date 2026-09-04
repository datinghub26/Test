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
        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'notifiable_type')) {
                $table->string('notifiable_type')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('notifications', 'notifiable_id')) {
                $table->unsignedBigInteger('notifiable_id')->nullable()->after('notifiable_type');
            }
            if (!Schema::hasColumn('notifications', 'data')) {
                $table->longText('data')->nullable()->after('notifiable_id');
            }
            if (!Schema::hasColumn('notifications', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('data');
            }
        });

        // Add index safely
        try {
            Schema::table('notifications', function (Blueprint $table) {
                $table->index(['notifiable_type', 'notifiable_id'], 'notifications_notifiable_index');
            });
        } catch (\Throwable $e) {
            // Index may already exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('notifications', 'notifiable_type')) $columnsToDrop[] = 'notifiable_type';
            if (Schema::hasColumn('notifications', 'notifiable_id')) $columnsToDrop[] = 'notifiable_id';
            if (Schema::hasColumn('notifications', 'data')) $columnsToDrop[] = 'data';
            if (Schema::hasColumn('notifications', 'read_at')) $columnsToDrop[] = 'read_at';

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
