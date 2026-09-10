<?php

namespace App\Http\Requests\Calendar;

use Illuminate\Foundation\Http\FormRequest;

class StoreCalendarEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('calendar.create');
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:event,meeting,reminder,task'],
            'title' => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string', 'max:2000'],
            'start_datetime' => ['required', 'date'],
            'end_datetime' => ['nullable', 'date', 'after_or_equal:start_datetime'],
            'all_day' => ['nullable', 'boolean'],
            'location' => ['nullable', 'string', 'max:191'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'status' => ['nullable', 'in:pending,in_progress,completed,cancelled'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'reminder_minutes_before' => ['nullable', 'integer', 'min:0', 'max:43200'],
            'is_recurring' => ['nullable', 'boolean'],
            'recurrence_type' => ['nullable', 'required_if:is_recurring,1', 'in:daily,weekly,monthly,yearly'],
            'recurrence_end_date' => ['nullable', 'date', 'after_or_equal:start_datetime'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'attendees' => ['nullable', 'array'],
            'attendees.*' => ['exists:users,id'],
            'related_type' => ['nullable', 'string', 'max:191'],
            'related_id' => ['nullable', 'integer'],
        ];
    }

    public function attributes(): array
    {
        return [
            'start_datetime' => 'start date/time',
            'end_datetime' => 'end date/time',
        ];
    }
}
