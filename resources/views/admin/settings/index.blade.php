@extends('layouts.app')

@section('title', 'Settings')

@section('content')
@php
    $settingVal = function ($settings, $group, $key, $default = '') {
        return optional(($settings[$group] ?? collect())->firstWhere('key', $key))->value ?? $default;
    };
@endphp

<h4 class="mb-3">Settings</h4>

<ul class="nav nav-pills mb-3">
    @foreach($groups as $group)
        <li class="nav-item">
            <a class="nav-link {{ $activeGroup === $group ? 'active' : '' }}" href="{{ route('admin.settings.index', ['tab' => $group]) }}">
                {{ ucfirst($group) }}
            </a>
        </li>
    @endforeach
</ul>

<div class="card shadow-sm" style="max-width:720px">
    <div class="card-body">

    @if($activeGroup === 'company')
        <form action="{{ route('admin.settings.update', 'company') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Company Name</label>
                    <input type="text" name="company_name" class="form-control" value="{{ $settingVal($settings, 'company', 'company_name') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Logo</label>
                    @if($logo = $settingVal($settings, 'company', 'company_logo'))
                        <div class="mb-2"><img src="{{ Storage::url($logo) }}" style="height:50px"></div>
                    @endif
                    <input type="file" name="company_logo" class="form-control" accept="image/*">
                </div>
                <div class="col-12">
                    <label class="form-label">GST Number</label>
                    <input type="text" name="gst_number" class="form-control" value="{{ $settingVal($settings, 'company', 'gst_number') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Address</label>
                    <textarea name="company_address" class="form-control" rows="2">{{ $settingVal($settings, 'company', 'company_address') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="company_phone" class="form-control" value="{{ $settingVal($settings, 'company', 'company_phone') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="company_email" class="form-control" value="{{ $settingVal($settings, 'company', 'company_email') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Website</label>
                    <input type="text" name="company_website" class="form-control" value="{{ $settingVal($settings, 'company', 'company_website') }}">
                </div>
            </div>
            <button class="btn btn-success mt-3"><i class="bi bi-check-lg me-1"></i> Save</button>
        </form>

    @elseif($activeGroup === 'invoice')
        <form action="{{ route('admin.settings.update', 'invoice') }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Invoice Prefix</label>
                    <input type="text" name="invoice_prefix" class="form-control" value="{{ $settingVal($settings, 'invoice', 'invoice_prefix', 'INV') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Consumer ID Prefix</label>
                    <input type="text" name="consumer_prefix" class="form-control" value="{{ $settingVal($settings, 'invoice', 'consumer_prefix', 'MILK') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Receipt Prefix</label>
                    <input type="text" name="receipt_prefix" class="form-control" value="{{ $settingVal($settings, 'invoice', 'receipt_prefix', 'RCPT') }}">
                </div>
            </div>
            <button class="btn btn-success mt-3"><i class="bi bi-check-lg me-1"></i> Save</button>
        </form>

    @elseif($activeGroup === 'general')
        <form action="{{ route('admin.settings.update', 'general') }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Currency Symbol</label>
                    <input type="text" name="currency_symbol" class="form-control" value="{{ $settingVal($settings, 'general', 'currency_symbol', '₹') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Timezone</label>
                    <input type="text" name="timezone" class="form-control" value="{{ $settingVal($settings, 'general', 'timezone', 'Asia/Kolkata') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Language</label>
                    <input type="text" name="language" class="form-control" value="{{ $settingVal($settings, 'general', 'language', 'en') }}">
                </div>
            </div>
            <button class="btn btn-success mt-3"><i class="bi bi-check-lg me-1"></i> Save</button>
        </form>

    @elseif($activeGroup === 'sms')
        <form action="{{ route('admin.settings.update', 'sms') }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">SMS Provider</label>
                    <select name="sms_provider" class="form-select">
                        @foreach(['none' => 'None', 'twilio' => 'Twilio', 'msg91' => 'MSG91', 'fast2sms' => 'Fast2SMS'] as $val => $label)
                            <option value="{{ $val }}" @selected($settingVal($settings, 'sms', 'sms_provider', 'none') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">Provider credentials (API keys/tokens) are set via environment variables (.env) for security, not stored here.</div>
                </div>
            </div>
            <button class="btn btn-success mt-3"><i class="bi bi-check-lg me-1"></i> Save</button>
        </form>

    @elseif($activeGroup === 'whatsapp')
        <form action="{{ route('admin.settings.update', 'whatsapp') }}" method="POST">
            @csrf @method('PUT')
            <div class="form-check form-switch">
                <input type="checkbox" name="whatsapp_enabled" value="1" class="form-check-input" id="waEnabled"
                       @checked($settingVal($settings, 'whatsapp', 'whatsapp_enabled', '0') == '1')>
                <label class="form-check-label" for="waEnabled">Enable WhatsApp "Click to Chat" links on bills & receipts</label>
            </div>
            <div class="form-text mt-1">Uses wa.me links — no WhatsApp Business API account required.</div>
            <button class="btn btn-success mt-3"><i class="bi bi-check-lg me-1"></i> Save</button>
        </form>

    @elseif($activeGroup === 'backup')
        <form action="{{ route('admin.settings.update', 'backup') }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Backup Reminder (days)</label>
                    <input type="number" min="1" name="backup_reminder_days" class="form-control" value="{{ $settingVal($settings, 'backup', 'backup_reminder_days', 7) }}">
                    <div class="form-text">A reminder notification is created for Admins every N days if no backup has been run.</div>
                </div>
            </div>
            <button class="btn btn-success mt-3"><i class="bi bi-check-lg me-1"></i> Save</button>
        </form>
    @endif

    </div>
</div>
@endsection
