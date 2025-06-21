<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تأكيد استلام الطلبية</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Cairo', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .confirmation-container {
            max-width: 800px;
            width: 100%;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .confirmation-header {
            background: linear-gradient(135deg, #5b86e5 0%, #36d1dc 100%);
            color: white;
            padding: 40px;
            text-align: center;
            border-bottom: 5px solid #fff;
            position: relative;
        }

        .confirmation-header .icon {
            font-size: 4em;
            margin-bottom: 15px;
            animation: bounce 1.5s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-20px); }
            60% { transform: translateY(-10px); }
        }

        .confirmation-header h1 {
            margin: 0;
            font-weight: 700;
            font-size: 2em;
        }

        .confirmation-body {
            padding: 30px 40px;
        }

        .order-details {
            margin-bottom: 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            font-size: 1.1em;
        }

        .order-details .detail-item {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 12px;
            border-left: 5px solid #5b86e5;
        }
        
        .order-details .detail-item strong {
            color: #333;
            display: block;
            margin-bottom: 5px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .items-table th, .items-table td {
            padding: 15px;
            text-align: right;
            border-bottom: 1px solid #eef2f7;
        }

        .items-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #555;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }
        
        .badge-shipped {
            background-color: #fd7e14;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
        }

        .confirmation-footer {
            padding: 30px 40px;
            background-color: #f8f9fa;
            text-align: center;
        }

        .confirmation-footer p {
            color: #6c757d;
            margin-bottom: 20px;
        }

        .confirm-button {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 1.2em;
            font-weight: 700;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        .confirm-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        }
        
        .confirm-button i {
            margin-left: 10px;
        }
    </style>
</head>
<body>

<div class="confirmation-container">
    <div class="confirmation-header">
        <div class="icon">
            <i class="fas fa-box-check"></i>
        </div>
        <h1>الخطوة الأخيرة: تأكيد استلام الطلبية</h1>
    </div>
    <div class="confirmation-body">
        <div class="order-details">
            <div class="detail-item">
                <strong>رقم الطلبية:</strong>
                <span>#{{ $order->order_number }}</span>
            </div>
            <div class="detail-item">
                <strong>المورد:</strong>
                <span>{{ $order->supplier->contact_person_name }}</span>
            </div>
             <div class="detail-item">
                <strong>تاريخ الشحن:</strong>
                <span>{{ \Carbon\Carbon::parse($order->delevery_date)->format('Y/m/d') }}</span>
            </div>
             <div class="detail-item">
                <strong>حالة الطلب الحالية:</strong>
                <span class="badge badge-shipped">تم الشحن</span>
            </div>
        </div>

        <h3 style="text-align: right; margin-top: 40px; color: #333;">محتويات الطلبية:</h3>
        <div class="table-responsive">
            <table class="items-table">
                <thead>
                    <tr>
                        <th>الدواء</th>
                        <th>الكمية المطلوبة</th>
                        <th>السعر الوحدوي</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderItems as $item)
                    <tr>
                        <td>{{ $item->medicine->medicine_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->unit_price, 2) }} ل.س</td>
                        <td>{{ number_format($item->total_price, 2) }} ل.س</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: #f1f5f9; font-weight: bold;">
                        <td colspan="3">الإجمالي الكلي للطلبية</td>
                        <td>{{ number_format($order->calculateTotal(), 2) }} ل.س</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="confirmation-footer">
        <p>بالنقر على الزر أدناه، أنت تؤكد أنك قد استلمت جميع المنتجات المذكورة أعلاه بحالة جيدة.</p>
        <form action="{{ route('complete', $order->id) }}" method="POST" id="confirmationForm">
            @csrf
            <button type="submit" class="confirm-button">
                تأكيد الاستلام وإكمال الطلبية
                <i class="fas fa-check-circle"></i>
            </button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
<script>
    document.getElementById('confirmationForm').addEventListener('submit', function(e) {
        // Prevent default form submission to show animation first
        e.preventDefault();
        
        const form = this;

        // Trigger confetti
        confetti({
            particleCount: 150,
            spread: 90,
            origin: { y: 0.6 }
        });

        // Add a small delay to let the user see the confetti before redirecting
        setTimeout(function() {
            form.submit();
        }, 800); 
    });
</script>
</body>
</html>
