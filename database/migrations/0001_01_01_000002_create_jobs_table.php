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
        /*
        |--------------------------------------------------------------------------
        | Jobs Table
        |--------------------------------------------------------------------------
        */
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();

            // Indexed column - keep it within the MySQL key limit.
            $table->string('queue', 191)->index();

            $table->longText('payload');

            $table->unsignedSmallInteger('attempts');

            $table->unsignedInteger('reserved_at')->nullable();

            $table->unsignedInteger('available_at');

            $table->unsignedInteger('created_at');
        });


        /*
        |--------------------------------------------------------------------------
        | Job Batches Table
        |--------------------------------------------------------------------------
        */
        Schema::create('job_batches', function (Blueprint $table) {
            // Laravel batch IDs are normally UUIDs.
            // 36 characters is sufficient and very safe for a primary key.
            $table->string('id', 36)->primary();

            $table->string('name', 191);

            $table->integer('total_jobs');

            $table->integer('pending_jobs');

            $table->integer('failed_jobs');

            $table->longText('failed_job_ids');

            $table->mediumText('options')->nullable();

            $table->integer('cancelled_at')->nullable();

            $table->integer('created_at');

            $table->integer('finished_at')->nullable();
        });


        /*
        |--------------------------------------------------------------------------
        | Failed Jobs Table
        |--------------------------------------------------------------------------
        */
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();

            // UUID = 36 characters.
            // No reason to use VARCHAR(255) here.
            $table->string('uuid', 36)->unique();

            /*
            |--------------------------------------------------------------------------
            | These two columns are part of a composite index below.
            |
            | connection 100 + queue 100 + timestamp
            | stays safely below the 1000-byte MySQL limit.
            |--------------------------------------------------------------------------
            */
            $table->string('connection', 100);

            $table->string('queue', 100);

            $table->longText('payload');

            $table->longText('exception');

            $table->timestamp('failed_at')->useCurrent();

            /*
            |--------------------------------------------------------------------------
            | Composite Index
            |--------------------------------------------------------------------------
            */
            $table->index([
                'connection',
                'queue',
                'failed_at'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_jobs');

        Schema::dropIfExists('job_batches');

        Schema::dropIfExists('jobs');
    }
};