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
        Schema::table('leads', function (Blueprint $table) {
            $table->index('created_at', 'leads_created_at_index');
            $table->index('status', 'leads_status_index');
            $table->index('type', 'leads_type_index');
            $table->index(['type', 'created_at'], 'leads_type_created_at_index');
            $table->index(['status', 'created_at'], 'leads_status_created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex('leads_created_at_index');
            $table->dropIndex('leads_status_index');
            $table->dropIndex('leads_type_index');
            $table->dropIndex('leads_type_created_at_index');
            $table->dropIndex('leads_status_created_at_index');
        });
    }
};
