<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>🚨 تنبيه انتهاء صلاحية الأدوية | نظام الصيدلية</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; direction: rtl; text-align: right; background: linear-gradient(135deg, #2b2b2b, #161616); padding: 20px; color: #fff; }
        .container { max-width: 800px; background: rgba(255, 255, 255, 0.08); margin: auto; padding: 25px; border-radius: 12px; box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.3); backdrop-filter: blur(12px); }
        h2 { text-align: center; font-size: 28px; font-weight: bold; color: #ffcc00; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; backdrop-filter: blur(5px); }
        .table th, .table td { padding: 14px; text-align: center; border: 1px solid rgba(255, 255, 255, 0.2); color: #fff; }
        .table th { background-color: rgba(255, 255, 255, 0.15); font-weight: bold; }
        .highlight { font-weight: bold; color: #ffcc00; }
        .btn { display: block; width: 260px; margin: 20px auto; padding: 12px; text-align: center; background: linear-gradient(135deg, #ffcc00, #ff8800); color: #161616; font-weight: bold; text-decoration: none; border-radius: 8px; box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.4); transition: 0.3s; }
        .btn:hover { background: linear-gradient(135deg, #ff8800, #ff5500); transform: scale(1.08); }
    </style>
</head>
<body>
    <div class="container">
        <h2>🚨 قائمة الأدوية القريبة من انتهاء الصلاحية 🚨</h2>

        <table class="table">
            <tr>
                <th>اسم الدواء</th>
                <th>رقم الدفعة</th>
                <th>الكمية في الدفعة</th>
                <th>سعر الوحدة</th>
                <th>الفئة</th>
                <th>تاريخ الانتهاء</th>
                <th>الأيام المتبقية</th>
            </tr>
            <tr>
                <td>{{ $medicine_name }}</td>
                <td>{{ $batch_number ?? '-' }}</td>
                <td>{{ $batch_quantity ?? '-' }}</td>
                <td>{{ $batch_unit_price ?? '-' }}</td>
                <td>{{ $category }}</td>
                <td class="highlight">{{ $expiry_date }}</td>
                <td class="highlight">{{ $expiry_date_diffForHumans }}</td>
            </tr>
        </table>

        <a href="{{ url('/inventory') }}" class="btn">📦 مراجعة حالة المخزون</a>

        <p>🔔 يرجى اتخاذ إجراء في أسرع وقت لمنع استخدام الأدوية منتهية الصلاحية.</p>
        <p>📧 للاستفسارات، يمكنك التواصل مع الإدارة عبر البريد الإلكتروني: <strong>{{ $supportEmail }}</strong></p>
    </div>
</body>
</html>
