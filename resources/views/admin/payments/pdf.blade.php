<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipt {{ $payment->receipt_number }}</title>
    <style>
        * { font-family: 'Helvetica', Arial, sans-serif; box-sizing: border-box; }
        body { color: #222; font-size: 13px; margin: 0; padding: 30px; }
        .header { display: table; width: 100%; margin-bottom: 20px; border-bottom: 2px solid #2e7d32; padding-bottom: 12px; }
        .header .left { display: table-cell; width: 60%; vertical-align: top; }
        .header .right { display: table-cell; width: 40%; vertical-align: top; text-align: right; }
        .company-name { font-size: 20px; font-weight: bold; color: #1b5e20; }
        .muted { color: #666; }
        .receipt-title { font-size: 16px; font-weight: bold; margin-bottom: 4px; }
        table.details { width: 100%; margin-top: 20px; border-collapse: collapse; }
        table.details td { padding: 8px 0; border-bottom: 1px solid #eee; }
        table.details td.val { text-align: right; }
        .amount-box { margin-top: 20px; padding: 14px; background: #f1f8f2; border: 1px solid #2e7d32; text-align: center; }
        .amount-box .label { color: #666; font-size: 11px; }
        .amount-box .value { font-size: 22px; font-weight: bold; color: #1b5e20; }
        .footer { margin-top: 40px; text-align: center; color: #888; font-size: 10px; border-top: 1px solid #eee; padding-top: 10px; }
        .signature { margin-top: 50px; text-align: right; }
        .signature .line { display: inline-block; border-top: 1px solid #333; width: 180px; padding-top: 4px; text-align: center; font-size: 11px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <div class="left">
            <div class="company-name">{{ setting('company_name', 'Milk Dairy') }}</div>
            <div class="muted">{{ setting('company_address') }}</div>
            <div class="muted">{{ setting('company_phone') }}</div>
        </div>
        <div class="right">
            <div class="receipt-title">PAYMENT RECEIPT</div>
            <div class="muted">{{ $payment->receipt_number }}</div>
            <div class="muted">Date: {{ $payment->payment_date->format('d M Y') }}</div>
        </div>
    </div>

    <table class="details">
        <tr><td>Received From</td><td class="val">{{ $payment->customer->name }} ({{ $payment->customer->consumer_id }})</td></tr>
        <tr><td>Payment Method</td><td class="val">{{ $payment->paymentMethod->name }}</td></tr>
        <tr><td>Reference Number</td><td class="val">{{ $payment->reference_number ?: '—' }}</td></tr>
        <tr><td>Applied To</td><td class="val">{{ $payment->is_advance ? 'Advance / No specific bill' : ($payment->bill->invoice_number ?? '—') }}</td></tr>
        <tr><td>Amount</td><td class="val">{{ money($payment->amount) }}</td></tr>
        <tr><td>Discount</td><td class="val">-{{ money($payment->discount) }}</td></tr>
        <tr><td>Adjustment</td><td class="val">{{ money($payment->adjustment) }}</td></tr>
    </table>

    <div class="amount-box">
        <div class="label">AMOUNT RECEIVED</div>
        <div class="value">{{ money($payment->netAmount()) }}</div>
    </div>

    <div class="signature">
        <div class="line">Authorized Signature</div>
    </div>

    <div class="footer">
        This is a system-generated receipt from {{ setting('company_name', 'Milk Dairy') }}. Thank you for your payment.
    </div>
</body>
</html>
