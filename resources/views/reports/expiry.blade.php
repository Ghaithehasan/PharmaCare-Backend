<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Expired & Expiring Medicines Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1, h2 { color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
        .urgent { color: #c0392b; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Expired & Expiring Medicines Report</h1>
    <p>Date: {{ now()->format('Y-m-d') }}</p>

    <h2>Expired Medicines</h2>
    <p>Total Expired: {{ $data['expired']['count'] }} | Total Value: {{ number_format($data['expired']['total_value'], 2) }}</p>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Medicine Name</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Expiry Date</th>
                <th>Total Value</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['expired']['items'] as $i => $item)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $item['medicine_name'] }}</td>
                    <td>{{ $item['category'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ $item['expiry_date'] }}</td>
                    <td>{{ number_format($item['total_value'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6">No expired medicines.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Medicines Expiring Soon</h2>
    <p>Total Expiring Soon: {{ $data['expiring_soon']['count'] }} | Total Value: {{ number_format($data['expiring_soon']['total_value'], 2) }}</p>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Medicine Name</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Expiry Date</th>
                <th>Days to Expiry</th>
                <th>Total Value</th>
                <th>Urgent</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['expiring_soon']['items'] as $i => $item)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $item['medicine_name'] }}</td>
                    <td>{{ $item['category'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ $item['expiry_date'] }}</td>
                    <td>{{ $item['days_to_expiry'] }}</td>
                    <td>{{ number_format($item['total_value'], 2) }}</td>
                    <td>@if($item['is_urgent']) <span class="urgent">Yes</span> @else No @endif</td>
                </tr>
            @empty
                <tr><td colspan="8">No medicines expiring soon.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
