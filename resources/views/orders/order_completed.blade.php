<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم تأكيد استلام الطلبية</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <style>
        body {
            background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
            font-family: 'Cairo', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
        }
        .thanks-container {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(80, 112, 255, 0.12);
            max-width: 600px;
            width: 100%;
            padding: 48px 32px 40px 32px;
            text-align: center;
            animation: fadeIn 1s;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .thanks-icon {
            font-size: 4em;
            color: #28a745;
            margin-bottom: 18px;
            animation: pop 1.2s;
        }
        @keyframes pop {
            0% { transform: scale(0.7); opacity: 0; }
            60% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(1); }
        }
        h1 {
            color: #222;
            font-size: 2em;
            font-weight: 700;
            margin-bottom: 12px;
        }
        .thanks-message {
            color: #555;
            font-size: 1.15em;
            margin-bottom: 30px;
        }
        .order-summary {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 18px 0 10px 0;
            margin-bottom: 24px;
            font-size: 1.05em;
        }
        .order-summary strong {
            color: #333;
        }
        .invoice-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .invoice-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        .invoice-header {
            font-size: 1.3em;
            font-weight: 700;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
        }
        .invoice-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            position: relative;
            z-index: 1;
        }
        .invoice-item {
            text-align: center;
            padding: 12px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            backdrop-filter: blur(10px);
        }
        .invoice-item strong {
            display: block;
            font-size: 0.9em;
            margin-bottom: 4px;
            opacity: 0.9;
        }
        .invoice-item span {
            font-size: 1.1em;
            font-weight: 600;
        }
        .due-date-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 12px;
            margin-top: 16px;
            color: #856404;
            font-size: 0.95em;
            position: relative;
            z-index: 1;
        }
        .back-btn {
            background: linear-gradient(90deg, #5b86e5 0%, #36d1dc 100%);
            color: #fff;
            border: none;
            padding: 12px 36px;
            border-radius: 30px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .back-btn:hover {
            background: linear-gradient(90deg, #36d1dc 0%, #5b86e5 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(80, 112, 255, 0.18);
        }
        .download-btn {
            background: linear-gradient(90deg, #11998e 0%, #38ef7d 100%);
            color: #fff;
            border: none;
            padding: 14px 32px;
            border-radius: 30px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            margin-left: 12px;
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
        }
        .download-btn:hover {
            background: linear-gradient(90deg, #38ef7d 0%, #11998e 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(17, 153, 142, 0.4);
        }
        .buttons-container {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        @media (max-width: 480px) {
            .invoice-details {
                grid-template-columns: 1fr;
            }
            .thanks-container {
                padding: 32px 20px;
            }
            .buttons-container {
                flex-direction: column;
                align-items: center;
            }
            .download-btn {
                margin-left: 0;
                margin-top: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="thanks-container">
        <div class="thanks-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1>تم تأكيد استلام الطلبية بنجاح!</h1>
        <div class="thanks-message">
            شكرًا لك على تأكيد استلام الطلبية.<br>
            تم إنشاء فاتورة جديدة تلقائياً.
        </div>
        
        <div class="invoice-section">
            <div class="invoice-header">
                <i class="fas fa-file-invoice-dollar ml-2"></i>
                فاتورة جديدة
            </div>
            <div class="invoice-details">
                <div class="invoice-item">
                    <strong>رقم الفاتورة</strong>
                    <span>{{ $invoice->invoice_number }}</span>
                </div>
                <div class="invoice-item">
                    <strong>المبلغ الإجمالي</strong>
                    <span>{{ number_format($invoice->total_amount, 2) }} ليرة سوري</span>
                </div>
                <div class="invoice-item">
                    <strong>تاريخ الفاتورة</strong>
                    <span>{{ $invoice->invoice_date->format('Y/m/d') }}</span>
                </div>
                <div class="invoice-item">
                    <strong>تاريخ الاستحقاق</strong>
                    <span>{{ $invoice->due_date->format('Y/m/d') }}</span>
                </div>
            </div>
            <div class="due-date-warning">
                <i class="fas fa-clock ml-1"></i>
                <strong>تنبيه:</strong> لديك شهر واحد لتسديد المبلغ المطلوب
            </div>
        </div>

        <div class="order-summary">
            <div><strong>رقم الطلبية:</strong> #{{ $order->order_number }}</div>
            <div><strong>المورد:</strong> {{ $order->supplier->contact_person_name }}</div>
            <div><strong>تاريخ التأكيد:</strong> {{ now()->format('Y/m/d H:i') }}</div>
        </div>
        
        <div class="buttons-container">
        <a href="/" class="back-btn"><i class="fas fa-home ml-1"></i>العودة للصفحة الرئيسية</a>
            <a href="{{ route('invoices.download', $invoice->id) }}" class="download-btn"><i class="fas fa-download ml-1"></i>تحميل الفاتورة</a>
        </div>
    </div>
</body>
</html> 