<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Invoice</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #667eea;
        }
        .header h1 {
            color: #667eea;
            margin: 0;
            font-size: 28px;
        }
        .invoice-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .label {
            font-weight: 600;
            color: #495057;
        }
        .value {
            color: #212529;
        }
        .total-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 25px;
        }
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        .btn:hover {
            background: #5a6fd8;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-unpaid {
            background: #fff3cd;
            color: #856404;
        }
        .status-paid {
            background: #d4edda;
            color: #155724;
        }
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>New Invoice Generated</h1>
            <p>A new invoice has been created for your order</p>
        </div>

        <div class="invoice-details">
            <div class="detail-row">
                <span class="label">Invoice Number:</span>
                <span class="value">{{ $invoice->invoice_number }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Order Number:</span>
                <span class="value">{{ $invoice->order->order_number }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Supplier:</span>
                <span class="value">{{ $invoice->order->supplier->company_name }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Invoice Date:</span>
                <span class="value">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Due Date:</span>
                <span class="value">{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Status:</span>
                <span class="value">
                    <span class="status-badge status-{{ $invoice->status }}">
                        @if($invoice->status == 'unpaid')
                            Unpaid
                        @elseif($invoice->status == 'paid')
                            Paid
                        @else
                            Cancelled
                        @endif
                    </span>
                </span>
            </div>
        </div>

        <div class="total-section">
            <div>Total Amount</div>
            <div class="total-amount">${{ number_format($invoice->total_amount, 2) }}</div>
            <div>Due in 30 days</div>
        </div>

        @if($invoice->notes)
        <div style="background: #e3f2fd; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <strong>Notes:</strong> {{ $invoice->notes }}
        </div>
        @endif

        <div style="text-align: center; margin: 25px 0;">
            <p><strong>Invoice PDF is attached to this email.</strong></p>
            <p>Please review the attached invoice and process the payment within the due date.</p>
        </div>

        <div class="footer">
            <p>Thank you for your business!</p>
            <p>This email was sent automatically from Pharmacy Management System</p>
            <p>Generated on {{ now()->format('M d, Y \a\t H:i') }}</p>
        </div>
    </div>
</body>
</html> 