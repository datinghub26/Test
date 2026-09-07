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
        if (!Schema::hasTable('offer_clicks')) {
            Schema::create('offer_clicks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('offer_id', 150)->nullable()->index();
                $table->string('provider', 100)->nullable()->index();
                $table->string('ip', 45)->nullable();
                $table->timestamp('clicked_at')->nullable()->index();
                $table->timestamps();
            });
        }

        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'lead_time_seconds')) {
                $table->integer('lead_time_seconds')->nullable()->after('status')->index();
            }
            if (!Schema::hasColumn('leads', 'clicked_at')) {
                $table->timestamp('clicked_at')->nullable()->after('lead_time_seconds');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'clicked_at')) {
                $table->dropColumn('clicked_at');
            }
            if (Schema::hasColumn('leads', 'lead_time_seconds')) {
                $table->dropColumn('lead_time_seconds');
            }
        });

        Schema::dropIfExists('offer_clicks');
    }
};
