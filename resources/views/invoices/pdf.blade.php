<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- تصميم عصري ومبسط -->
    <style>
        :root {
            --primary: #4f46e5;
            --bg: #f9fafb;
            --text: #1e293b;
            --gray: #64748b;
            --success: #16a34a;
            --warning: #f59e0b;
            --danger: #dc2626;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            margin: 0;
            padding: 32px;
        }

        .invoice-card {
            max-width: 850px;
            margin: auto;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.07);
        }

        .header {
            background: var(--primary);
            color: white;
            padding: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            margin: 0;
            font-size: 2rem;
        }

        .meta {
            text-align: right;
        }

        .meta .number {
            font-size: 1.3rem;
            font-weight: 600;
        }

        .content {
            padding: 32px 40px;
        }

        .two-cols {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 32px;
        }

        .info-box h3 {
            margin-bottom: 12px;
            font-size: 1rem;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .info-box .row {
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
        }

        .info-box .row span {
            font-size: 0.95rem;
        }

        .status-badge {
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-unpaid { background: #fef3c7; color: #92400e; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-partial { background: #fde68a; color: #92400e; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 24px;
        }

        table th {
            background: var(--primary);
            color: white;
            font-weight: 600;
            text-align: left;
            padding: 12px;
            font-size: 0.85rem;
        }

        table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        .summary {
            margin-top: 28px;
            text-align: right;
        }

        .summary .line {
            display: flex;
            justify-content: space-between;
            font-size: 1.1rem;
            margin-bottom: 6px;
        }

        .summary .total {
            font-size: 1.6rem;
            font-weight: 700;
            margin-top: 12px;
            border-top: 1px solid #ddd;
            padding-top: 12px;
        }

        .notes {
            background: #f0fdf4;
            padding: 16px;
            margin-top: 24px;
            border-left: 4px solid var(--success);
        }

        footer {
            background: #1e293b;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 0.85rem;
        }

        @media print {body { background: white; padding: 0; }
            .invoice-card { box-shadow: none; border-radius: 0; margin: 0; }
        }
    </style>
</head>
<body>
<div class="invoice-card">
    <div class="header">
        <h1>INVOICE</h1>
        <div class="meta">
            <div class="number">#{{ $invoice->invoice_number }}</div>
            <div>{{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('F d, Y') : '--' }}</div>
        </div>
    </div>

    <div class="content">
        <div class="two-cols">
            <div class="info-box">
                <h3>Invoice Details</h3>
                <div class="row"><span>Invoice #:</span><span>{{ $invoice->invoice_number }}</span></div>
                <div class="row"><span>Date:</span><span>{{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') : '--' }}</span></div>
                <div class="row"><span>Due:</span><span>{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') : '--' }}</span></div>
                <div class="row">
                    <span>Status:</span>
                    <span>
                        <span class="status-badge status-{{ $invoice->status }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </span>
                </div>
            </div>

            <div class="info-box">
                <h3>Order Info</h3>
                <div class="row"><span>Order #:</span><span>{{ $invoice->order->order_number }}</span></div>
                <div class="row"><span>Supplier:</span><span>{{ $invoice->order->supplier->company_name }}</span></div>
                <div class="row"><span>Phone:</span><span>{{ $invoice->order->supplier->phone }}</span></div>
                <div class="row"><span>Order Date:</span><span>{{ $invoice->order && $invoice->order->order_date ? \Carbon\Carbon::parse($invoice->order->order_date)->format('M d, Y') : '--' }}</span></div>
            </div>
        </div>

        <table>
            <thead>
            <tr>
                <th>Medicine</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            @foreach($invoice->order->orderItems as $item)
                <tr>
                    <td>{{ $item->medicine->medicine_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->unit_price, 2) }}</td>
                    <td>${{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="summary">
            <div class="line"><span>Total Amount:</span><span class="total">${{ number_format($invoice->total_amount, 2) }}</span></div>
        </div>

        @if($invoice->notes)
        <div class="notes">
            <strong>Notes:</strong><br>
            {{ $invoice->notes }}
        </div>
        @endif
    </div>

    <footer>
        <p>Thank you for your business</p>
        <p>Generated on {{ now() ? \Carbon\Carbon::parse(now())->format('M d, Y \a\t H:i') : '' }}</p>
    </footer>
</div>
</body>
</html>