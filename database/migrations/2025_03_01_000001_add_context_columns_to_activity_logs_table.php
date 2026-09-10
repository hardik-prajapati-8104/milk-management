<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Enriches activity_logs with parsed request/device context.
     * Populated centrally inside ActivityLog::record(), so every one of the
     * ~40 existing call sites across controllers/services starts getting
     * this data automatically with zero changes to those call sites.
     */
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('method', 10)->nullable()->after('ip_address');
            $table->string('url', 2048)->nullable()->after('method');
            $table->string('browser', 100)->nullable()->after('user_agent');
            $table->string('browser_version', 50)->nullable()->after('browser');
            $table->string('platform', 100)->nullable()->after('browser_version'); // OS
            $table->string('device_type', 20)->nullable()->after('platform'); // desktop, mobile, tablet, bot
            $table->string('session_id', 100)->nullable()->after('device_type');

            $table->index('ip_address');
            $table->index('device_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['ip_address']);
            $table->dropIndex(['device_type']);
            $table->dropIndex(['created_at']);

            $table->dropColumn([
                'method', 'url', 'browser', 'browser_version',
                'platform', 'device_type', 'session_id',
            ]);
        });
    }
};
