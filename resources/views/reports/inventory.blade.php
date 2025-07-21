<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير جرد المخزون</title>
    <style>
        @font-face {
            font-family: 'Cairo';
            src: url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');
        }
        
        body {
            font-family: 'Cairo', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
            direction: rtl;
        }
        
        .header {
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        
        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        
        .section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .section h2 {
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 22px;
            font-weight: 600;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 18px;
            font-weight: 600;
        }
        
        .stat-card .value {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-card .label {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .table th {
            background: #34495e;
            color: white;
            padding: 12px;
            text-align: right;
            font-weight: 600;
        }
        
        .table td {
            padding: 12px;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .table tr:hover {
            background-color: #e3f2fd;
        }
        
        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-success {
            background-color: #27ae60;
            color: white;
        }
        
        .badge-warning {
            background-color: #f39c12;
            color: white;
        }
        
        .badge-danger {
            background-color: #e74c3c;
            color: white;
        }
        
        .badge-info {
            background-color: #3498db;
            color: white;
        }
        
        .chart-container {
            margin: 20px 0;
            text-align: center;
        }
        
        .recommendations {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .recommendations h3 {
            margin: 0 0 15px 0;
            font-size: 20px;
            font-weight: 600;
        }
        
        .recommendations ul {
            margin: 0;
            padding-right: 20px;
        }
        
        .recommendations li {
            margin-bottom: 8px;
            font-size: 16px;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            background: #34495e;
            color: white;
            border-radius: 10px;
        }
        
        .footer p {
            margin: 5px 0;
            font-size: 14px;
        }
        
        @media print {
            body {
                background: white;
            }
            
            .section {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏥 تقرير جرد المخزون الشامل</h1>
        <p>نظام إدارة الصيدلية - {{ date('Y-m-d H:i') }}</p>
    </div>

    @if(isset($data['summary']))
    <div class="section">
        <h2>📊 ملخص عام</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="value">{{ $data['summary']['total_counts'] ?? 0 }}</div>
                <div class="label">إجمالي عمليات الجرد</div>
            </div>
            <div class="stat-card">
                <div class="value">{{ $data['summary']['total_items_checked'] ?? 0 }}</div>
                <div class="label">إجمالي العناصر المفحوصة</div>
            </div>
            <div class="stat-card">
                <div class="value">{{ number_format($data['summary']['accuracy_rate'] ?? 0, 1) }}%</div>
                <div class="label">نسبة الدقة</div>
            </div>
            <div class="stat-card">
                <div class="value">{{ $data['summary']['total_discrepancies'] ?? 0 }}</div>
                <div class="label">إجمالي الفروقات</div>
            </div>
        </div>
    </div>
    @endif

    @if(isset($data['discrepancies']))
    <div class="section">
        <h2>⚠️ تحليل الفروقات</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="value">{{ $data['discrepancies']['critical_discrepancies'] ?? 0 }}</div>
                <div class="label">فروقات حرجة</div>
            </div>
            <div class="stat-card">
                <div class="value">{{ $data['discrepancies']['moderate_discrepancies'] ?? 0 }}</div>
                <div class="label">فروقات متوسطة</div>
            </div>
            <div class="stat-card">
                <div class="value">{{ $data['discrepancies']['minor_discrepancies'] ?? 0 }}</div>
                <div class="label">فروقات بسيطة</div>
            </div>
            <div class="stat-card">
                <div class="value">{{ $data['discrepancies']['overstock_items'] ?? 0 }}</div>
                <div class="label">عناصر زائدة</div>
            </div>
        </div>
    </div>
    @endif

    @if(isset($data['category_analysis']))
    <div class="section">
        <h2>📋 تحليل التصنيفات</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>التصنيف</th>
                    <th>إجمالي العناصر</th>
                    <th>الفروقات</th>
                    <th>نسبة الفروقات</th>
                    <th>القيمة المفقودة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['category_analysis'] as $category => $info)
                <tr>
                    <td>{{ $category }}</td>
                    <td>{{ $info['total_items'] }}</td>
                    <td>{{ $info['discrepancies'] }}</td>
                    <td>
                        @php
                            $percentage = $info['total_items'] > 0 ? ($info['discrepancies'] / $info['total_items']) * 100 : 0;
                        @endphp
                        <span class="badge badge-{{ $percentage > 10 ? 'danger' : ($percentage > 5 ? 'warning' : 'success') }}">
                            {{ number_format($percentage, 1) }}%
                        </span>
                    </td>
                    <td>{{ number_format($info['total_value_loss'], 2) }} ريال</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(isset($data['recommendations']))
    <div class="recommendations">
        <h3>💡 التوصيات والتحسينات</h3>
        <ul>
            @foreach($data['recommendations'] as $recommendation)
            <li>{{ $recommendation }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="footer">
        <p>تم إنشاء هذا التقرير بواسطة نظام إدارة الصيدلية</p>
        <p>تاريخ الإنشاء: {{ date('Y-m-d H:i:s') }}</p>
        <p>جميع الحقوق محفوظة © {{ date('Y') }}</p>
    </div>
</body>
</html> 