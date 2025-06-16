<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap');
        
        body {
            font-family: 'Tajawal', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            -webkit-text-size-adjust: 100%;
            -webkit-font-smoothing: antialiased;
        }
        
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            box-sizing: border-box;
        }
        
        .header {
            background: linear-gradient(135deg, #1e88e5, #1565c0);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        
        .content {
            padding: 20px;
        }
        
        .order-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .order-info h2 {
            color: #1565c0;
            margin-top: 0;
            font-size: 20px;
            border-bottom: 2px solid #e3f2fd;
            padding-bottom: 10px;
            font-weight: 600;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }
        
        .info-label {
            font-weight: 500;
            color: #666;
            font-size: 15px;
        }
        
        .info-value {
            font-weight: 700;
            color: #333;
            font-size: 15px;
        }
        
        .medicines-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .medicines-table th {
            background: #e3f2fd;
            color: #1565c0;
            font-weight: 600;
            text-align: right;
            padding: 12px 8px;
            font-size: 14px;
            border: 1px solid #e3f2fd;
        }
        
        .medicines-table td {
            padding: 10px 8px;
            border: 1px solid #eee;
            font-size: 14px;
            text-align: right;
        }
        
        .medicines-table tr:last-child td {
            border-bottom: 1px solid #eee;
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .total-section {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            text-align: left;
            margin-top: 20px;
        }
        
        .total-amount {
            font-size: 22px;
            font-weight: 700;
            color: #1565c0;
        }
        
        .footer {
            background: #f8f9fa;
            padding: 30px 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #eee;
        }
        
        .footer-message {
            margin-bottom: 25px;
            line-height: 1.8;
            color: #555;
        }
        
        .footer-message h3 {
            color: #1565c0;
            font-size: 18px;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .footer-message p {
            margin: 10px 0;
            font-size: 15px;
        }
        
        .dashboard-button {
            display: inline-block;
            background: linear-gradient(135deg, #1e88e5, #1565c0);
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            margin: 20px 0;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .dashboard-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .footer-note {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 13px;
            color: #888;
        }

        .highlight {
            color: #1565c0;
            font-weight: 500;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            text-align: center;
            min-width: 80px;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        @media screen and (max-width: 480px) {
            .container {
                margin: 10px;
                width: auto;
            }
            
            .content {
                padding: 15px;
            }
            
            .medicines-table {
                margin: 15px 0;
            }
            
            .medicines-table th,
            .medicines-table td {
                padding: 8px 6px;
                font-size: 13px;
            }

            .header {
                padding: 20px 15px;
            }
            
            .header h1 {
                font-size: 20px;
            }
            
            .order-info {
                padding: 15px;
            }
            
            .order-info h2 {
                font-size: 18px;
            }
            
            .info-label, .info-value {
                font-size: 14px;
            }
            
            .total-amount {
                font-size: 20px;
            }
            
            .status-badge {
                padding: 4px 12px;
                font-size: 12px;
                min-width: 70px;
            }

            .footer {
                padding: 25px 15px;
            }

            .footer-message h3 {
                font-size: 16px;
            }

            .footer-message p {
                font-size: 14px;
            }

            .dashboard-button {
                padding: 10px 25px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Order</h1>
            <p>Hello {{ $supplier->contact_person_name }}</p>
        </div>
        
        <div class="content">
            <div class="order-info">
                <h2>Order Information</h2>
                <div class="info-row">
                    <span class="info-label">Order Number:</span>
                    <span class="info-value">{{ $order->order_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Order Date:</span>
                    <span class="info-value">{{ $order->created_at->format('Y-m-d') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Order Status:</span>
                    <span class="info-value">
                        <span class="status-badge status-{{ strtolower($order->status) }}">
                            {{ $order->status }}
                        </span>
                    </span>
                </div>
            </div>

            <h2 style="color: #1565c0; margin-top: 30px; font-size: 18px;">Requested Medicines</h2>
            <div class="table-container">
                <table class="medicines-table">
                    <thead>
                        <tr>
                            <th style="width: 40%">Medicine Name</th>
                            <th style="width: 20%">Quantity</th>
                            <th style="width: 20%">Unit Price</th>
                            <th style="width: 20%">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medicines as $medicine)
                        <tr>
                            <td>{{ $medicine['medicine_name'] }}</td>
                            <td>{{ $medicine['quantity'] }}</td>
                            <td>{{ number_format($medicine['unit_price']) }} SYR</td>
                            <td>{{ number_format($medicine['total_price']) }} SYR</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="total-section">
                <div class="info-row">
                    <span class="info-label">Total Amount:</span>
                    <span class="total-amount">{{ number_format($total_price) }} SYR</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="footer-message">
                <h3>Thank You for Your Partnership</h3>
                <p>We appreciate your continued support and commitment to providing quality medicines.</p>
                <p>Your prompt attention to this order will help us serve our customers better.</p>
            </div>

            <a href="{{ route('home') }}" class="dashboard-button">
                Go to Dashboard
            </a>

            <div class="footer-note">
                <p>This is an automated email, please do not reply</p>
                <p>For any inquiries, please contact our support team</p>
            </div>
        </div>
    </div>
</body>
</html>

