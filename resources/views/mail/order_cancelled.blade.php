<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تم إلغاء طلبك</title>
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
      background: linear-gradient(90deg, #ef4444, #f87171);
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
      background: #fef2f2;
      padding: 16px 20px;
      border-radius: 12px;
      border: 1px dashed #fca5a5;
      margin: 24px 0;
      text-align: right;
      direction: rtl;
    }
    .order-info h3 {
      margin-top: 0;
      color: #dc2626;
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
      background: #ef4444;
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
    .cancellation-reason {
      background: #fff5f5;
      border: 1px solid #fed7d7;
      border-radius: 8px;
      padding: 12px 16px;
      margin: 16px 0;
      text-align: right;
    }
  </style>
</head>
<body>
  <div class="email-container">
    <div class="email-header">
      <h1>❌ تم إلغاء طلبك</h1>
      <p>نعتذر عن عدم تمكننا من إكمال طلبك</p>
    </div>

    <div class="email-body">
      <div class="logo">
        <img src="https://cdn-icons-png.flaticon.com/512/2972/2972185.png" alt="pharmacy logo">
      </div>

      <p>مرحبًا،</p>
      <p>تم إلغاء طلبية من منصة <strong>المورد</strong> من قبل المورد. نود إبلاغكم بهذا التغيير.</p>

      <div class="order-info">
        <h3>تفاصيل الطلب الملغي:</h3>
        <div class="order-item">
          <span class="order-label">رقم الطلبية:</span> #{{ $order_number }}
        </div>
        <div class="order-item">
          <span class="order-label">اسم المورد:</span> {{ $supplier_name }}
        </div>
        <div class="order-item">
          <span class="order-label">تاريخ الطلب:</span> {{ $order_date }}
        </div>
        <div class="order-item">
          <span class="order-label">إجمالي الطلب:</span> {{ $order_total }} ليرة سوري
        </div>
        <div class="order-item">
          <span class="order-label">تاريخ الإلغاء:</span> {{ $cancelled_at }}
        </div>
      </div>

      @if($cancellation_reason)
      <div class="cancellation-reason">
        <strong>سبب الإلغاء:</strong><br>
        {{ $cancellation_reason }}
      </div>
      @endif

      <p style="text-align:center;">
        <a href="" class="cta-button">طلب جديد</a>
      </p>

      <p>إذا كان لديك أي استفسار حول هذا الإلغاء، يرجى التواصل مع فريق الدعم الفني. نعتذر مرة أخرى عن أي إزعاج قد تسببنا به.</p>
      
      <p>شكرًا لثقتك في منصة <strong>المورد</strong>.</p>
    </div>

    <div class="footer">
      📬 هذا بريد تلقائي من نظام المورد | لا تقم بالرد عليه
    </div>
  </div>
</body>
</html> 