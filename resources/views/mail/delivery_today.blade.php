<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>موعد تسليم الطلبية اليوم</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background: #f4f7fc;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 520px;
            margin: 40px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(80, 112, 255, 0.10);
            overflow: hidden;
            border: 1px solid #e3e9f7;
        }
        .email-header {
            background: linear-gradient(90deg, #36d1dc 0%, #5b86e5 100%);
            color: #fff;
            padding: 32px 24px 18px 24px;
            text-align: center;
        }
        .email-header h2 {
            margin: 0 0 10px 0;
            font-size: 1.5em;
            font-weight: 700;
        }
        .email-body {
            padding: 32px 24px 24px 24px;
            color: #333;
        }
        .order-info {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 24px;
            border: 1px dashed #36d1dc;
        }
        .order-info p {
            margin: 8px 0;
            font-size: 1.08em;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(90deg, #36d1dc 0%, #5b86e5 100%);
            color: #fff;
            text-decoration: none;
            padding: 14px 36px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1.1em;
            margin: 20px 0 0 0;
            transition: background 0.3s;
        }
        .cta-button:hover {
            background: linear-gradient(90deg, #5b86e5 0%, #36d1dc 100%);
        }
        .footer {
            text-align: center;
            font-size: 13px;
            color: #999;
            padding: 18px 0 10px 0;
        }
        .icon {
            font-size: 2.5em;
            margin-bottom: 10px;
            color: #36d1dc;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <div class="icon">🚚</div>
            <h2>تنبيه هام: اليوم موعد تسليم طلبيتك!</h2>
        </div>
        <div class="email-body">
            <div class="order-info">
                <p><strong>رقم الطلبية:</strong> #{{ $order->order_number }}</p>
                <p><strong>المورد:</strong> {{ $order->supplier->contact_person_name }}</p>
                <p><strong>تاريخ التسليم:</strong> {{ \Carbon\Carbon::parse($order->delevery_date)->format('Y/m/d') }}</p>
            </div>
            <p>يرجى التأكد من استلام الطلبية من المورد في الوقت المحدد. يمكنك تأكيد استلام الطلبية مباشرة عبر الزر التالي:</p>
            <div style="text-align:center;">
                <a href="http://localhost:8000/Accept-the-order/{{$order->id}}" class="cta-button">تأكيد استلام الطلبية</a>
            </div>
        </div>
        <div class="footer">
            📬 هذا بريد تلقائي من نظام المورد | لا تقم بالرد عليه
        </div>
    </div>
</body>
</html>