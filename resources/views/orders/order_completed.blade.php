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
            padding: 0;
            margin: 0;
        }
        .thanks-container {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(80, 112, 255, 0.12);
            max-width: 500px;
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
            إذا كان لديك أي ملاحظات أو استفسارات، لا تتردد في التواصل مع فريق الدعم.
        </div>
        <div class="order-summary">
            <div><strong>رقم الطلبية:</strong> #{{ $order->order_number }}</div>
            <div><strong>المورد:</strong> {{ $order->supplier->contact_person_name }}</div>
            <div><strong>تاريخ التأكيد:</strong> {{ now()->format('Y/m/d H:i') }}</div>
        </div>
        <a href="/" class="back-btn"><i class="fas fa-home ml-1"></i>العودة للصفحة الرئيسية</a>
    </div>
</body>
</html> 