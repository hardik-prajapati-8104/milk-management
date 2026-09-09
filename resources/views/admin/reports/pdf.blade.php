<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        * { font-family: 'Helvetica', Arial, sans-serif; }
        body { font-size: 11px; color: #222; padding: 20px; }
        h2 { color: #1b5e20; margin-bottom: 4px; }
        .muted { color: #666; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f8f2; text-align: left; padding: 6px 8px; border-bottom: 1px solid #ccc; }
        td { padding: 6px 8px; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <h2>{{ setting('company_name', 'Milk Dairy') }}</h2>
    <div class="muted">{{ ucwords(str_replace('-', ' ', $title)) }} — generated {{ now()->format('d M Y H:i') }}</div>

    <table>
        <thead>
            <tr>
                @foreach($headings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($headings) }}">No data.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
