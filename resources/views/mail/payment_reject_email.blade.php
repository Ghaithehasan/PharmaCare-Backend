<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رفض دفعة</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .header .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .content {
            padding: 30px;
        }
        .alert-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .alert-box h3 {
            color: #856404;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }
        .info-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #dc3545;
        }
        .info-item h4 {
            margin: 0 0 8px 0;
            color: #495057;
            font-size: 14px;
            font-weight: 600;
        }
        .info-item p {
            margin: 0;
            color: #6c757d;
            font-size: 16px;
            font-weight: 500;
        }
        .payment-details {
            background: #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .payment-details h3 {
            color: #495057;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
        }
        .detail-value {
            color: #6c757d;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 15px;
        }
        .btn:hover {
            background: #c82333;
        }
        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">❌</div>
            <h1>تم رفض الدفعة</h1>
            <p>يرجى مراجعة التفاصيل أدناه</p>
        </div>

        <div class="content">
            <div class="alert-box">
                <h3>⚠️ تنبيه مهم</h3>
                <p>تم رفض الدفعة المرسلة من المورد. يرجى التواصل مع المورد لمعرفة السبب وإعادة إرسال الدفعة بالشكل الصحيح.</p>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <h4>رقم الفاتورة</h4>
                    <p>{{ $invoice->invoice_number }}</p>
                </div>
                <div class="info-item">
                    <h4>رقم الطلبية</h4>
                    <p>{{ $order->order_number }}</p>
                </div>
                <div class="info-item">
                    <h4>اسم المورد</h4>
                    <p>{{ $supplier->contact_person_name }}</p>
                </div>
                <div class="info-item">
                    <h4>تاريخ الدفع</h4>
                    <p>{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y/m/d') }}</p>
                </div>
            </div>

            <div class="payment-details">
                <h3>تفاصيل الدفعة المرفوضة</h3>
                <div class="detail-row">
                    <span class="detail-label">المبلغ المدفوع:</span>
                    <span class="detail-value">{{ number_format($payment->paid_amount, 2) }} ليرة سوري</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">طريقة الدفع:</span>
                    <span class="detail-value">{{ $payment->payment_method }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">تاريخ إرسال الدفعة:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($payment->created_at)->format('Y/m/d H:i') }}</span>
                </div>
                @if($payment->notes)
                <div class="detail-row">
                    <span class="detail-label">سبب الرفض:</span>
                    <span class="detail-value">{{ $payment->notes }}</span>
                </div>
                @endif
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <p style="color: #6c757d; margin-bottom: 20px;">
                    يرجى التواصل مع المورد لإعادة إرسال الدفعة بالشكل الصحيح
                </p>
                <a href="#" class="btn">تواصل مع المورد</a>
            </div>
        </div>

        <div class="footer">
            <p>هذا الإيميل تم إرساله تلقائياً من نظام إدارة الفواتير</p>
            <p>صيدلية الهدى - جميع الحقوق محفوظة © {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>
