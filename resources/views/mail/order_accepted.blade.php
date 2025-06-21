<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تهانينا! طلبك قيد التنفيذ</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Cairo', sans-serif;
      background: #f1f5f9;
      margin: 0;
      padding: 0;
    }
    .email-container {
      max-width: 620px;
      margin: 40px auto;
      background: #fff;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    }
    .email-header {
      background: linear-gradient(90deg, #2f80ed, #56ccf2);
      color: #fff;
      padding: 32px 24px;
      text-align: center;
    }
    .email-header h1 {
      margin: 0;
      font-size: 24px;
    }
    .email-body {
      padding: 32px 24px;
      color: #333;
    }
    .order-info {
      background: #f7faff;
      padding: 16px 20px;
      border-radius: 12px;
      border: 1px dashed #90caf9;
      margin: 24px 0;
      text-align: right;
      direction: rtl;
    }
    .order-info h3 {
      margin-top: 0;
      color: #1565c0;
      text-align: right;
    }
    .order-item {
      margin: 10px 0;
      font-size: 16px;
      text-align: right;
      direction: rtl;
    }
    .order-label {
      display: inline-block;
      font-weight: bold;
      min-width: 120px;
      color: #555;
      text-align: right;
    }
    .cta-button {
      display: inline-block;
      background: #2f80ed;
      color: #fff;
      text-decoration: none;
      padding: 14px 26px;
      border-radius: 8px;
      font-weight: bold;
      font-size: 16px;
      margin: 20px 0;
    }
    .footer {
      text-align: center;
      font-size: 13px;
      color: #999;
      padding: 20px;
    }
    .logo {
      text-align: center;
      margin-bottom: 20px;
    }
    .logo img {
      width: 64px;
      height: auto;
    }
  </style>
</head>
<body>
  <div class="email-container">
    <div class="email-header">
      <h1>🎉 تم قبول طلبك بنجاح</h1>
      <p>ونعمل حاليًا على تحضيره من أجلك</p>
    </div>

    <div class="email-body">
      <div class="logo">
        <img src="https://cdn-icons-png.flaticon.com/512/2972/2972185.png" alt="pharmacy logo">
      </div>

      <p>مرحبًا دكتور،</p>
      <p>نشكركم على طلبكم من منصة <strong>المورد</strong>. الطلبية أصبحت الآن في مرحلة التجهيز، ونعمل جاهدين على إيصالها في الوقت المحدد.</p>

      <div class="order-info">
        <h3>تفاصيل الطلب:</h3>
        <div class="order-item">
          <span class="order-label">رقم الطلبية:</span> #{{ $order_number }}
        </div>
        <div class="order-item">
          <span class="order-label">التاريخ المتوقع للتسليم:</span> {{ $delivery_date }}
        </div>
        <div class="order-item">
          <span class="order-label">حالة الطلب:</span> تم القبول ✅
        </div>
      </div>

      <p style="text-align:center;">
        <a href="" class="cta-button">عرض تفاصيل الطلبية</a>
      </p>

      <p>إذا كان لديك أي استفسار، يرجى عدم التردد بالتواصل معنا. نتمنى لك دوام الصحة.</p>
    </div>

    <div class="footer">
      📬 هذا بريد تلقائي من نظام المورد | لا تقم بالرد عليه
    </div>
  </div>
</body>
</html>