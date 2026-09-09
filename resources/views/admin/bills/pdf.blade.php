<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $bill->invoice_number }}</title>
    <style>
        * { font-family: 'Helvetica', Arial, sans-serif; box-sizing: border-box; }
        body { color: #222; font-size: 12px; margin: 0; padding: 30px; }
        .header { display: table; width: 100%; margin-bottom: 20px; border-bottom: 2px solid #2e7d32; padding-bottom: 12px; }
        .header .left { display: table-cell; width: 60%; vertical-align: top; }
        .header .right { display: table-cell; width: 40%; vertical-align: top; text-align: right; }
        .company-name { font-size: 20px; font-weight: bold; color: #1b5e20; }
        .muted { color: #666; }
        .invoice-title { font-size: 16px; font-weight: bold; margin-bottom: 4px; }
        table.info { width: 100%; margin-bottom: 16px; }
        table.info td { vertical-align: top; padding: 4px 0; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table.items th { background: #f1f8f2; text-align: left; padding: 6px 8px; font-size: 11px; border-bottom: 1px solid #ccc; }
        table.items td { padding: 6px 8px; border-bottom: 1px solid #eee; font-size: 11px; }
        table.items td.num, table.items th.num { text-align: right; }
        table.summary { width: 45%; margin-left: 55%; }
        table.summary td { padding: 3px 0; font-size: 12px; }
        table.summary td.val { text-align: right; }
        .total-row td { border-top: 2px solid #2e7d32; font-weight: bold; font-size: 14px; padding-top: 6px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 3px; font-size: 11px; color: #fff; background: #2e7d32; }
        .footer { margin-top: 30px; text-align: center; color: #888; font-size: 10px; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="left">
            <div class="company-name">{{ setting('company_name', 'Milk Dairy') }}</div>
            <div class="muted">{{ setting('company_address') }}</div>
            <div class="muted">{{ setting('company_phone') }} @if(setting('company_email')) · {{ setting('company_email') }} @endif</div>
            @if(setting('gst_number'))
                <div class="muted">GSTIN: {{ setting('gst_number') }}</div>
            @endif
        </div>
        <div class="right">
            <div class="invoice-title">INVOICE</div>
            <div class="muted">{{ $bill->invoice_number }}</div>
            <div class="muted">Bill #: {{ $bill->bill_number }}</div>
            <div class="muted">Date: {{ $bill->created_at->format('d M Y') }}</div>
            <div style="margin-top:6px"><span class="badge">{{ ucwords(str_replace('_', ' ', $bill->status)) }}</span></div>
        </div>
    </div>

    <table class="info">
        <tr>
            <td style="width:50%">
                <strong>Bill To</strong><br>
                {{ $bill->customer->name }}<br>
                {{ $bill->customer->consumer_id }}<br>
                {{ $bill->customer->address }}<br>
                {{ $bill->customer->mobile }}
            </td>
            <td style="width:50%; text-align:right">
                <strong>Billing Period</strong><br>
                {{ $bill->period_start->format('d M Y') }} – {{ $bill->period_end->format('d M Y') }}<br><br>
                <strong>Due Date</strong><br>
                {{ $bill->due_date?->format('d M Y') ?? '—' }}
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th class="num">Qty (L)</th>
                <th class="num">Rate</th>
                <th class="num">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bill->items as $item)
                <tr>
                    <td>{{ optional($item->item_date)->format('d M') }}</td>
                    <td>{{ $item->description }}</td>
                    <td class="num">{{ number_format($item->quantity, 2) }}</td>
                    <td class="num">{{ money($item->rate) }}</td>
                    <td class="num">{{ money($item->amount) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary">
        <tr><td>Milk Amount</td><td class="val">{{ money($bill->milk_amount) }}</td></tr>
        <tr><td>Extra Charges</td><td class="val">{{ money($bill->extra_charges) }}</td></tr>
        <tr><td>Delivery Charges</td><td class="val">{{ money($bill->delivery_charges) }}</td></tr>
        <tr><td>Penalty</td><td class="val">{{ money($bill->penalty) }}</td></tr>
        <tr><td>Discount</td><td class="val">-{{ money($bill->discount) }}</td></tr>
        <tr><td>Previous Due</td><td class="val">{{ money($bill->previous_due) }}</td></tr>
        <tr><td>Advance Adjusted</td><td class="val">-{{ money($bill->advance_adjusted) }}</td></tr>
        <tr><td>GST ({{ $bill->gst_percent }}%)</td><td class="val">{{ money($bill->gst_amount) }}</td></tr>
        <tr class="total-row"><td>Total Due</td><td class="val">{{ money($bill->total_amount) }}</td></tr>
        <tr><td>Paid</td><td class="val">{{ money($bill->paid_amount) }}</td></tr>
        <tr><td><strong>Outstanding</strong></td><td class="val"><strong>{{ money($bill->outstanding_amount) }}</strong></td></tr>
    </table>

    <div class="footer">
        This is a system-generated invoice from {{ setting('company_name', 'Milk Dairy') }}. Thank you for your business.
    </div>
</body>
</html>
