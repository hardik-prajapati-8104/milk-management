<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One table backs four calendar record "types" (event, meeting, reminder,
     * task) so they can be queried, filtered and rendered together on a
     * single FullCalendar feed while still carrying type-specific fields
     * (attendees for meetings, priority/status for tasks, etc).
     */
    public function up(): void
    {
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['event', 'meeting', 'reminder', 'task'])->default('event')->index();
            $table->string('title', 191);
            $table->text('description')->nullable();

            $table->dateTime('start_datetime')->index();
            $table->dateTime('end_datetime')->nullable();
            $table->boolean('all_day')->default(false);

            $table->string('location', 191)->nullable();
            $table->string('color', 7)->nullable(); // hex override; falls back to type default

            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');

            // Reminder / notification lead time, in minutes before start_datetime.
            $table->unsignedInteger('reminder_minutes_before')->nullable();
            $table->boolean('reminder_sent')->default(false);

            // Simple recurrence (daily/weekly/monthly/yearly) with an optional end date.
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurrence_type', ['daily', 'weekly', 'monthly', 'yearly'])->nullable();
            $table->date('recurrence_end_date')->nullable();
            $table->unsignedBigInteger('recurrence_parent_id')->nullable()->index();

            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            // Optional link to any other record (customer, employee, supplier, bill...).
            $table->nullableMorphs('related');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['start_datetime', 'end_datetime']);
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
