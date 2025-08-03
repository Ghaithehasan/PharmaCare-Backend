<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إشعار دفع جديد</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            direction: rtl;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 25px;
            font-weight: 500;
        }
        .payment-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            border-right: 4px solid #667eea;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
            font-size: 14px;
        }
        .detail-value {
            color: #212529;
            font-weight: 500;
            font-size: 14px;
        }
        .amount {
            color: #28a745;
            font-weight: 700;
            font-size: 16px;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .highlight {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: center;
        }
        .highlight h3 {
            margin: 0 0 10px 0;
            font-size: 18px;
        }
        .highlight p {
            margin: 0;
            font-size: 16px;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💰 إشعار دفع جديد</h1>
            <p>تم إضافة مدفوعة جديدة إلى فاتورتك</p>
        </div>

        <div class="content">
            <div class="greeting">
                مرحباً {{ $supplier_name }}،
            </div>

            <p>نود إعلامك بأنه تم إضافة مدفوعة جديدة إلى فاتورتك. إليك التفاصيل:</p>

            <div class="payment-details">
                <div class="detail-row">
                    <span class="detail-label">رقم الفاتورة:</span>
                    <span class="detail-value">{{ $invoice_number }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">رقم الطلبية:</span>
                    <span class="detail-value">{{ $order_number }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">مبلغ الدفع:</span>
                    <span class="detail-value amount">{{ number_format($payment_amount, 2) }} ل.س</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">طريقة الدفع:</span>
                    <span class="detail-value">
                        @if($payment_method == 'cash')
                            نقداً
                        @elseif($payment_method == 'bank_transfer')
                            تحويل بنكي
                        @else
                            {{ $payment_method }}
                        @endif
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">تاريخ الدفع:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($payment_date)->format('Y-m-d') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">حالة الدفع:</span>
                    <span class="status-badge
                        @if($payment_status == 'pending') status-pending
                        @elseif($payment_status == 'confirmed') status-confirmed
                        @elseif($payment_status == 'rejected') status-rejected
                        @endif">
                        @if($payment_status == 'pending')
                            معلق
                        @elseif($payment_status == 'confirmed')
                            مؤكد
                        @elseif($payment_status == 'rejected')
                            مرفوض
                        @else
                            {{ $payment_status }}
                        @endif
                    </span>
                </div>
            </div>

            <div class="highlight">
                <h3>📊 ملخص الفاتورة</h3>
                <p>إجمالي الفاتورة: {{ number_format($total_invoice_amount, 2) }} ل.س</p>
                <p>المبلغ المتبقي: {{ number_format($remaining_amount, 2) }} ل.س</p>
            </div>

            <p style="color: #6c757d; font-size: 14px; margin-top: 30px;">
                إذا كان لديك أي استفسار حول هذه المدفوعة، يرجى التواصل معنا.
            </p>
        </div>

        <div class="footer">
            <p>تم إرسال هذا الإشعار تلقائياً من نظام إدارة الصيدلية</p>
            <p>© {{ date('Y') }} جميع الحقوق محفوظة</p>
        </div>
    </div>
</body>
</html>

