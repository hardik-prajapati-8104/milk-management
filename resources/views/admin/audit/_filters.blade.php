@php
    $showAction = $showAction ?? false;
    $showStatus = $showStatus ?? null;
@endphp
<div class="d-flex flex-wrap gap-2 mb-3">
    <select class="form-select form-select-sm w-auto" id="{{ $prefix }}UserFilter">
        <option value="">All Users</option>
        @foreach($users as $u)
            <option value="{{ $u->id }}">{{ $u->name }}</option>
        @endforeach
    </select>

    @if($showAction)
    <select class="form-select form-select-sm w-auto" id="{{ $prefix }}ActionFilter">
        <option value="">All Actions</option>
        <option value="created">Created</option>
        <option value="updated">Updated</option>
        <option value="deleted">Deleted</option>
        <option value="login">Login</option>
        <option value="logout">Logout</option>
    </select>
    @endif

    @if($showStatus)
    <select class="form-select form-select-sm w-auto" id="{{ $prefix }}StatusFilter">
        <option value="">All Statuses</option>
        @foreach($showStatus as $val => $label)
            <option value="{{ $val }}">{{ $label }}</option>
        @endforeach
    </select>
    @endif

    <input type="date" class="form-control form-control-sm w-auto" id="{{ $prefix }}DateFrom" title="From date">
    <input type="date" class="form-control form-control-sm w-auto" id="{{ $prefix }}DateTo" title="To date">
</div>
