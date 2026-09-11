@extends('layouts.app')

@section('title', 'Calendar')

@push('styles')
<style>
    #calendarEl { background: #fff; }
    .fc-event { cursor: pointer; border: none; }
    .fc .fc-daygrid-day.fc-day-today,
    .fc .fc-timegrid-col.fc-day-today { background: rgba(46,125,50,.06); }
    .legend-dot { display:inline-block; width:.65rem; height:.65rem; border-radius:50%; margin-right:.35rem; vertical-align:middle; }
    .type-field { display:none; }
    .type-field.active { display:block; }
    .upcoming-item { border-left:3px solid var(--brand); padding:.4rem .6rem; margin-bottom:.4rem; background:#f8f9fa; border-radius:.25rem; }
    .upcoming-item .days { font-size:.72rem; }
</style>
@endpush

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0"><i class="bi bi-calendar3 me-2"></i>Calendar</h4>
    <div class="d-flex gap-2 flex-wrap">
        @can('holidays.view')
        <a href="{{ route('admin.holidays.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-calendar-heart me-1"></i> Manage Holidays
        </a>
        @endcan
        @can('birthdays.view')
        <a href="{{ route('admin.birthdays.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-gift me-1"></i> Manage Birthdays
        </a>
        @endcan
        @can('create', App\Models\CalendarEvent::class)
        <div class="dropdown">
            <button class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-plus-lg me-1"></i> New
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item js-new" href="#" data-type="event"><i class="bi bi-calendar-event me-2"></i>Event</a></li>
                <li><a class="dropdown-item js-new" href="#" data-type="meeting"><i class="bi bi-people me-2"></i>Meeting</a></li>
                <li><a class="dropdown-item js-new" href="#" data-type="reminder"><i class="bi bi-bell me-2"></i>Reminder</a></li>
                <li><a class="dropdown-item js-new" href="#" data-type="task"><i class="bi bi-check2-square me-2"></i>Task</a></li>
            </ul>
        </div>
        @endcan
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-xl-9">
        <div class="card shadow-sm mb-3">
            <div class="card-body py-2">
                <div class="d-flex flex-wrap align-items-center gap-3 small">
                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input js-type-filter" type="checkbox" value="event" id="fltEvent" checked>
                        <label class="form-check-label" for="fltEvent"><span class="legend-dot" style="background:#0d6efd"></span>Events</label>
                    </div>
                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input js-type-filter" type="checkbox" value="meeting" id="fltMeeting" checked>
                        <label class="form-check-label" for="fltMeeting"><span class="legend-dot" style="background:#6f42c1"></span>Meetings</label>
                    </div>
                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input js-type-filter" type="checkbox" value="reminder" id="fltReminder" checked>
                        <label class="form-check-label" for="fltReminder"><span class="legend-dot" style="background:#fd7e14"></span>Reminders</label>
                    </div>
                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input js-type-filter" type="checkbox" value="task" id="fltTask" checked>
                        <label class="form-check-label" for="fltTask"><span class="legend-dot" style="background:#198754"></span>Tasks</label>
                    </div>
                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input js-type-filter" type="checkbox" value="holiday" id="fltHoliday" checked>
                        <label class="form-check-label" for="fltHoliday"><span class="legend-dot" style="background:#dc3545"></span>Holidays</label>
                    </div>
                    <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input js-type-filter" type="checkbox" value="birthday" id="fltBirthday" checked>
                        <label class="form-check-label" for="fltBirthday"><span class="legend-dot" style="background:#d63384"></span>Birthdays</label>
                    </div>
                    <div class="form-check form-check-inline mb-0 ms-auto">
                        <input class="form-check-input" type="checkbox" id="fltMine">
                        <label class="form-check-label" for="fltMine">My items only</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div id="calendarEl"></div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-3">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white"><i class="bi bi-gift text-danger me-1"></i> Upcoming Birthdays</div>
            <div class="card-body">
                @forelse($upcomingBirthdays as $b)
                    <div class="upcoming-item">
                        <div class="fw-semibold">{{ $b['name'] }}</div>
                        <div class="text-muted days">{{ $b['date']->format('d M') }} &middot; turning {{ $b['turning'] }} &middot;
                            {{ $b['days_until'] === 0 ? 'Today!' : $b['days_until'] . ' day(s)' }}
                        </div>
                    </div>
                @empty
                    <p class="text-muted small mb-0">No birthdays in the next 30 days.</p>
                @endforelse
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white"><i class="bi bi-calendar-heart text-danger me-1"></i> Upcoming Holidays</div>
            <div class="card-body">
                @forelse($upcomingHolidays as $h)
                    <div class="upcoming-item">
                        <div class="fw-semibold">{{ $h['name'] }}</div>
                        <div class="text-muted days">{{ $h['date']->format('d M Y') }} &middot; {{ ucfirst($h['type']) }} &middot;
                            {{ $h['days_until'] === 0 ? 'Today!' : $h['days_until'] . ' day(s)' }}
                        </div>
                    </div>
                @empty
                    <p class="text-muted small mb-0">No holidays in the next 60 days.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Create / Edit modal --}}
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="eventForm" class="modal-content">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" id="eventId">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle">New Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="formErrors" class="alert alert-danger d-none"></div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Type</label>
                        <select class="form-select" name="type" id="fType">
                            <option value="event">Event</option>
                            <option value="meeting">Meeting</option>
                            <option value="reminder">Reminder</option>
                            <option value="task">Task</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" id="fTitle" required maxlength="191">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="fDescription" rows="2" maxlength="2000"></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Start <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" name="start_datetime" id="fStart" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">End</label>
                        <input type="datetime-local" class="form-control" name="end_datetime" id="fEnd">
                    </div>

                    <div class="col-md-4">
                        <div class="form-check mt-4 pt-1">
                            <input class="form-check-input" type="checkbox" name="all_day" id="fAllDay" value="1">
                            <label class="form-check-label" for="fAllDay">All day</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Priority</label>
                        <select class="form-select" name="priority" id="fPriority">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="fStatus">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" name="location" id="fLocation" maxlength="191">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Color</label>
                        <input type="color" class="form-control form-control-color w-100" name="color" id="fColor" value="#0d6efd">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Remind before (min)</label>
                        <input type="number" min="0" max="43200" class="form-control" name="reminder_minutes_before" id="fReminder">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Assigned to</label>
                        <select class="form-select" name="assigned_to" id="fAssignedTo">
                            <option value="">— Unassigned —</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 type-field" id="fieldAttendees" data-types="meeting">
                        <label class="form-label">Attendees</label>
                        <select class="form-select" name="attendees[]" id="fAttendees" multiple>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_recurring" id="fRecurring" value="1">
                            <label class="form-check-label" for="fRecurring">Repeats</label>
                        </div>
                    </div>
                    <div class="col-md-4 type-field" id="fieldRecurType" data-recurring="1">
                        <label class="form-label">Repeat</label>
                        <select class="form-select" name="recurrence_type" id="fRecurType">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                    <div class="col-md-4 type-field" id="fieldRecurEnd" data-recurring="1">
                        <label class="form-label">Repeat until</label>
                        <input type="date" class="form-control" name="recurrence_end_date" id="fRecurEnd">
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <div>
                    <button type="button" class="btn btn-outline-danger btn-sm d-none" id="btnDelete">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                    <div class="btn-group btn-group-sm d-none" id="statusQuickActions">
                        <button type="button" class="btn btn-outline-success js-mark-status" data-status="completed">Mark Complete</button>
                        <button type="button" class="btn btn-outline-secondary js-mark-status" data-status="in_progress">In Progress</button>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i> Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6/index.global.min.js"></script>
<script>
$(function () {
    const canEdit = @json(auth()->user()->can('create', App\Models\CalendarEvent::class));
    const feedUrl = "{{ route('admin.calendar.feed') }}";
    const storeUrl = "{{ route('admin.calendar.store') }}";
    const showUrlBase = "{{ url('admin/calendar') }}";

    $('#fAttendees, #fAssignedTo').select2({ dropdownParent: $('#eventModal'), width: '100%' });

    const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));

    function selectedTypes() {
        return $('.js-type-filter:checked').map(function () { return this.value; }).get();
    }

    function toggleTypeFields() {
        const type = $('#fType').val();
        $('#fieldAttendees').toggleClass('active', type === 'meeting');
        $('#statusQuickActions').toggleClass('d-none', !$('#eventId').val() || !['task', 'meeting'].includes(type));
    }
    function toggleRecurFields() {
        const on = $('#fRecurring').is(':checked');
        $('[data-recurring="1"]').toggleClass('active', on);
    }
    $('#fType').on('change', toggleTypeFields);
    $('#fRecurring').on('change', toggleRecurFields);

    const calendarEl = document.getElementById('calendarEl');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        height: 'auto',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth' },
        initialView: 'dayGridMonth',
        editable: canEdit,
        selectable: canEdit,
        dayMaxEvents: 3,
        events: function (info, successCallback, failureCallback) {
            $.get(feedUrl, {
                start: info.startStr,
                end: info.endStr,
                types: selectedTypes(),
                mine_only: $('#fltMine').is(':checked') ? 1 : 0,
            }).done(successCallback).fail(failureCallback);
        },
        dateClick: function (info) {
            if (!canEdit) return;
            resetForm();
            $('#fStart').val(info.dateStr.length > 10 ? info.dateStr.slice(0, 16) : info.dateStr + 'T09:00');
            $('#eventModalTitle').text('New Event');
            eventModal.show();
        },
        eventDrop: function (info) { persistReschedule(info); },
        eventResize: function (info) { persistReschedule(info); },
        eventClick: function (info) {
            const props = info.event.extendedProps;
            if (props.source !== 'calendar_event' || !canEdit) {
                const bits = [];
                if (props.location) bits.push(`<p class="mb-1 small"><i class="bi bi-geo-alt"></i> ${props.location}</p>`);
                if (props.assignee) bits.push(`<p class="mb-1 small"><i class="bi bi-person"></i> ${props.assignee}</p>`);
                if (props.description) bits.push(`<p class="text-muted small mb-0">${props.description}</p>`);
                Swal.fire({ title: info.event.title, html: bits.join(''), icon: 'info' });
                return;
            }
            loadForEdit(props.recordId);
        },
    });
    calendar.render();

    function persistReschedule(info) {
        const props = info.event.extendedProps;
        if (props.source !== 'calendar_event') { info.revert(); return; }
        $.ajax({
            url: showUrlBase + '/' + props.recordId + '/reschedule',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                start_datetime: info.event.startStr,
                end_datetime: info.event.endStr || null,
            },
        }).fail(function () {
            info.revert();
            Swal.fire('Could not reschedule', '', 'error');
        });
    }

    function resetForm() {
        $('#eventForm')[0].reset();
        $('#eventId').val('');
        $('#formMethod').val('POST');
        $('#fAttendees').val(null).trigger('change');
        $('#fAssignedTo').val(null).trigger('change');
        $('#formErrors').addClass('d-none').empty();
        $('#btnDelete').addClass('d-none');
        $('#statusQuickActions').addClass('d-none');
        toggleTypeFields();
        toggleRecurFields();
    }

    $('.js-new').on('click', function (e) {
        e.preventDefault();
        resetForm();
        $('#fType').val($(this).data('type'));
        $('#eventModalTitle').text('New ' + $(this).data('type').charAt(0).toUpperCase() + $(this).data('type').slice(1));
        toggleTypeFields();
        eventModal.show();
    });

    function loadForEdit(id) {
        $.getJSON(showUrlBase + '/' + id).done(function (event) {
            resetForm();
            $('#eventId').val(event.id);
            $('#formMethod').val('PUT');
            $('#fType').val(event.type);
            $('#fTitle').val(event.title);
            $('#fDescription').val(event.description);
            $('#fStart').val((event.start_datetime || '').replace(' ', 'T').slice(0, 16));
            $('#fEnd').val(event.end_datetime ? event.end_datetime.replace(' ', 'T').slice(0, 16) : '');
            $('#fAllDay').prop('checked', !!event.all_day);
            $('#fPriority').val(event.priority);
            $('#fStatus').val(event.status);
            $('#fLocation').val(event.location);
            $('#fColor').val(event.color || '#0d6efd');
            $('#fReminder').val(event.reminder_minutes_before);
            $('#fAssignedTo').val(event.assigned_to).trigger('change');
            $('#fAttendees').val((event.attendees || []).map(a => a.id)).trigger('change');
            $('#fRecurring').prop('checked', !!event.is_recurring);
            $('#fRecurType').val(event.recurrence_type);
            $('#fRecurEnd').val(event.recurrence_end_date ? event.recurrence_end_date.slice(0, 10) : '');
            $('#eventModalTitle').text('Edit ' + event.type.charAt(0).toUpperCase() + event.type.slice(1));
            $('#btnDelete').removeClass('d-none');
            toggleTypeFields();
            toggleRecurFields();
            eventModal.show();
        });
    }

    $('#eventForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#eventId').val();
        const url = id ? showUrlBase + '/' + id : storeUrl;
        const formData = $(this).serialize();

        $.ajax({
            url: url,
            method: 'POST', // Laravel method-spoofing via _method field
            data: formData,
            headers: { Accept: 'application/json' },
        }).done(function () {
            eventModal.hide();
            calendar.refetchEvents();
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Saved', showConfirmButton: false, timer: 1500 });
        }).fail(function (xhr) {
            const errors = xhr.responseJSON && xhr.responseJSON.errors ? xhr.responseJSON.errors : { error: ['Something went wrong.'] };
            $('#formErrors').removeClass('d-none').html(Object.values(errors).flat().join('<br>'));
        });
    });

    $('#btnDelete').on('click', function () {
        const id = $('#eventId').val();
        if (!id) return;
        Swal.fire({
            title: 'Delete this item?', icon: 'warning', showCancelButton: true,
            confirmButtonText: 'Yes, delete', confirmButtonColor: '#dc3545',
        }).then((r) => {
            if (!r.isConfirmed) return;
            $.ajax({
                url: showUrlBase + '/' + id,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                headers: { Accept: 'application/json' },
            }).done(function () {
                eventModal.hide();
                calendar.refetchEvents();
            });
        });
    });

    $('.js-mark-status').on('click', function () {
        const id = $('#eventId').val();
        if (!id) return;
        $.post(showUrlBase + '/' + id + '/status', { _token: '{{ csrf_token() }}', status: $(this).data('status') })
            .done(function () {
                eventModal.hide();
                calendar.refetchEvents();
            });
    });

    $('.js-type-filter, #fltMine').on('change', function () { calendar.refetchEvents(); });

    resetForm();
});
</script>
@endpush
