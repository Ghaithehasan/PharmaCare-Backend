<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>🚀 تنبيه المخزون | نظام إدارة الصيدلية</title>
    <style>
        body { font-family: Arial, sans-serif; direction: rtl; text-align: right; background-color: #f8f9fa; padding: 20px; }
        .container { max-width: 600px; background: #ffffff; margin: auto; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); }
        h2 { color: #dc3545; text-align: center; font-size: 24px; }
        p { font-size: 16px; color: #555; }
        .low-stock { color: red; font-weight: bold; }
        .btn { display: block; width: 250px; margin: 20px auto; padding: 12px; text-align: center; background-color: #dc3545; color: white; font-weight: bold; text-decoration: none; border-radius: 5px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        .table th { background-color: #007bff; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🚨 تنبيه المخزون 🚨</h2>
        <p>⚠️ يرجى الانتباه إلى الأدوية التالية التي تحتاج إلى إعادة الطلب داخل الصيدلية:</p>

        <table class="table">
            <tr>
                <th>الدواء</th>
                <th>الكمية المتبقية</th>
                <th>الحد الأدنى</th>
            </tr>
            <tr>
            <td>🔹 {{ $medicine_name }}</td>
                <td class="low-stock">{{ $quantity }}</td>
                <td>{{ $alert_quantity }}</td>
            </tr>
        </table>

        <a href="" class="btn">📦 مراجعة حالة المخزون</a>

        <p>🕒 يرجى التحقق من المخزون بشكل دوري لضمان توفر جميع الأدوية الأساسية في الصيدلية.</p>
        <p>📧 للاستفسارات أو الدعم، يمكنك التواصل مع الإدارة عبر البريد الإلكتروني: <strong>matrex663@gmail.com</strong></p>
    </div>
</body>
</html>