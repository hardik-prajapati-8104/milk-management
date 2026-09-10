<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | User
            |--------------------------------------------------------------------------
            | The users table MUST already exist before this migration runs.
            */
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Request Information
            |--------------------------------------------------------------------------
            */
            $table->string('method', 10);

            /*
             * IMPORTANT:
             *
             * Do NOT use 2048 here because endpoint is indexed.
             * 191 is safe for older MySQL + utf8mb4 configurations.
             */
            $table->string('endpoint', 191);

            /*
            |--------------------------------------------------------------------------
            | Request / Response
            |--------------------------------------------------------------------------
            */
            $table->json('request_payload')->nullable();

            $table->unsignedSmallInteger('response_status')->nullable();

            $table->unsignedInteger('response_time_ms')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Client Information
            |--------------------------------------------------------------------------
            */
            $table->string('ip_address', 45)->nullable();

            $table->string('user_agent', 191)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Timestamp
            |--------------------------------------------------------------------------
            */
            $table->timestamp('created_at')
                ->nullable()
                ->index();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index(
                'endpoint',
                'api_logs_endpoint_index'
            );

            $table->index(
                'response_status',
                'api_logs_response_status_index'
            );

            $table->index(
                'user_id',
                'api_logs_user_id_index'
            );

            $table->index(
                'method',
                'api_logs_method_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};