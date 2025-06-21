@extends('layouts.master')
@section('title')
    جميع الطلبات
@stop
@section('css')
<link href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.css" rel="stylesheet" type='text/css'>
<style>
    body { background: #f6f8fa; }
    .all-orders-card {
        background: linear-gradient(120deg, #f8fafc 60%, #e8f4fd 100%);
        border-radius: 18px;
        box-shadow: 0 6px 32px 0 rgba(0, 123, 255, 0.10), 0 1.5px 4px 0 rgba(0, 123, 255, 0.08);
        margin-bottom: 32px;
        padding: 28px 28px 18px 28px;
        transition: box-shadow 0.3s;
        border: none;
        position: relative;
        border-left: 4px solid #007bff;
    }
    .all-orders-card:hover {
        box-shadow: 0 12px 40px 0 rgba(0, 123, 255, 0.18), 0 2px 8px 0 rgba(0, 123, 255, 0.10);
    }
    .all-orders-card .order-header {
        display: flex;
        align-items: center;
        gap: 18px;
        margin-bottom: 20px;
    }
    .all-orders-card .order-header .icon {
        font-size: 2.2em;
        color: #007bff;
        background: #e8f4fd;
        border-radius: 50%;
        padding: 12px 16px;
        box-shadow: 0 2px 8px rgba(0,123,255,0.08);
    }
    .all-orders-card .order-header .order-title {
        font-size: 1.25em;
        font-weight: 700;
        color: #2c3e50;
    }
    .all-orders-card .order-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
        font-size: 1.05em;
    }
    .all-orders-card .order-info div {
        color: #444;
        padding: 8px 0;
    }
    .badge-pending {
        background: linear-gradient(90deg, #ffc107 0%, #ff9800 100%);
        color: #fff;
        font-size: 1em;
        padding: 8px 18px;
        border-radius: 16px;
        box-shadow: 0 1px 4px rgba(255,193,7,0.08);
        font-weight: 600;
        letter-spacing: 1px;
    }
    .badge-confirmed {
        background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
        color: #fff;
        font-size: 1em;
        padding: 8px 18px;
        border-radius: 16px;
        box-shadow: 0 1px 4px rgba(40,167,69,0.08);
        font-weight: 600;
        letter-spacing: 1px;
    }
    .badge-cancelled {
        background: linear-gradient(90deg, #e74a3b 0%, #f39c12 100%);
        color: #fff;
        font-size: 1em;
        padding: 8px 18px;
        border-radius: 16px;
        box-shadow: 0 1px 4px rgba(231,76,59,0.08);
        font-weight: 600;
        letter-spacing: 1px;
    }
    .badge-completed {
        background: linear-gradient(90deg, #6f42c1 0%, #9c27b0 100%);
        color: #fff;
        font-size: 1em;
        padding: 8px 18px;
        border-radius: 16px;
        box-shadow: 0 1px 4px rgba(111,66,193,0.08);
        font-weight: 600;
        letter-spacing: 1px;
    }
    .order-details {
        background: rgba(255,255,255,0.7);
        border-radius: 12px;
        padding: 20px;
        margin-top: 15px;
    }
    .order-details h6 {
        color: #2c3e50;
        margin-bottom: 15px;
        font-weight: 600;
        border-bottom: 2px solid #007bff;
        padding-bottom: 8px;
    }
    .table th, .table td { 
        vertical-align: middle; 
        text-align: center; 
    }
    .table th {
        background: #f8f9fa;
        color: #2c3e50;
        font-weight: 600;
    }
    .status-section {
        background: rgba(255,255,255,0.7);
        border-radius: 12px;
        padding: 20px;
        margin-top: 15px;
    }
    .status-section h6 {
        color: #2c3e50;
        margin-bottom: 15px;
        font-weight: 600;
        border-bottom: 2px solid #007bff;
        padding-bottom: 8px;
    }
    .delivery-info {
        background: linear-gradient(90deg, #e8f4fd, #f0f8ff);
        border-radius: 8px;
        padding: 15px;
        margin-top: 10px;
        border: 1px solid #b3d9ff;
    }
    .delivery-info h6 {
        color: #007bff;
        margin-bottom: 8px;
        font-weight: 600;
    }
    .delivery-info p {
        color: #666;
        margin: 0;
        line-height: 1.5;
    }
    .notes-section {
        background: rgba(255,255,255,0.7);
        border-radius: 12px;
        padding: 20px;
        margin-top: 15px;
    }
    .notes-section h6 {
        color: #2c3e50;
        margin-bottom: 15px;
        font-weight: 600;
        border-bottom: 2px solid #007bff;
        padding-bottom: 8px;
    }
    .notes-content {
        background: linear-gradient(90deg, #fff3cd, #fff8e1);
        border-radius: 8px;
        padding: 15px;
        margin-top: 10px;
        border: 1px solid #ffeaa7;
    }
    .notes-content p {
        color: #856404;
        margin: 0;
        line-height: 1.5;
    }
    .filter-section {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .filter-section h5 {
        color: #2c3e50;
        margin-bottom: 15px;
        font-weight: 600;
    }
    .filter-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .filter-btn {
        padding: 8px 16px;
        border-radius: 20px;
        border: 2px solid #007bff;
        background: transparent;
        color: #007bff;
        font-weight: 600;
        transition: all 0.3s;
        cursor: pointer;
    }
    .filter-btn:hover, .filter-btn.active {
        background: #007bff;
        color: white;
    }
</style>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الطلبات</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ جميع الطلبات</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a class="btn btn-sm btn-primary" href="{{ route('supplier.orders.exports_orders') }}">
                <i class="fas fa-file-download ml-1"></i>تصدير اكسيل
            </a>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="filter-section">
            <h5><i class="fas fa-filter ml-1"></i> تصفية الطلبات</h5>
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="all">جميع الطلبات</button>
                <button class="filter-btn" data-filter="pending">قيد الانتظار</button>
                <button class="filter-btn" data-filter="confirmed">مقبولة</button>
                <button class="filter-btn" data-filter="cancelled">ملغية</button>
                <button class="filter-btn" data-filter="completed">مكتملة</button>
            </div>
        </div>

        @if($orders->count() == 0)
            <div class="text-center py-5">
                <i class="fas fa-inbox" style="font-size: 4em; color: #007bff;"></i>
                <h4 class="mt-3 text-muted">لا توجد طلبات</h4>
                <p class="text-muted">لم يتم إنشاء أي طلبات بعد</p>
            </div>
        @else
            @foreach($orders as $order)
                <div class="all-orders-card" data-status="{{ $order->status }}">
                    <div class="order-header">
                        <span class="icon"><i class="fas fa-shopping-cart"></i></span>
                        <span class="order-title">طلبية رقم {{ $order->order_number }}</span>
                        @if($order->status == 'pending')
                            <span class="badge badge-pending ml-auto">قيد الانتظار</span>
                        @elseif($order->status == 'confirmed')
                            <span class="badge badge-confirmed ml-auto">مقبولة</span>
                        @elseif($order->status == 'cancelled')
                            <span class="badge badge-cancelled ml-auto">ملغية</span>
                        @elseif($order->status == 'completed')
                            <span class="badge badge-completed ml-auto">مكتملة</span>
                        @endif
                    </div>
                    <div class="order-info">
                        <div><i class="fas fa-store ml-1"></i> <strong>الصيدلية:</strong> صيدلية الهدى</div>
                        <div><i class="fas fa-calendar-alt ml-1"></i> <strong>تاريخ الطلب:</strong> {{ \Carbon\Carbon::parse($order->order_date)->format('Y/m/d') }}</div>
                        <div><i class="fas fa-clock ml-1"></i> <strong>آخر تحديث:</strong> {{ \Carbon\Carbon::parse($order->updated_at)->format('Y/m/d H:i') }}</div>
                    </div>
                    
                    @if($order->delevery_date)
                        <div class="status-section">
                            <h6><i class="fas fa-truck ml-1"></i> معلومات التوصيل</h6>
                            <div class="delivery-info">
                                <h6>تاريخ التوصيل المتوقع</h6>
                                <p>{{ \Carbon\Carbon::parse($order->delevery_date)->format('Y/m/d') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($order->note)
                        <div class="notes-section">
                            <h6><i class="fas fa-sticky-note ml-1"></i> الملاحظات</h6>
                            <div class="notes-content">
                                <p>{{ $order->note }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="order-details">
                        <h6><i class="fas fa-pills ml-1"></i> المنتجات المطلوبة</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>الاسم التجاري</th>
                                        <th>الاسم العلمي</th>
                                        <th>الكمية</th>
                                        <th>السعر</th>
                                        <th>الإجمالي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->orderItems as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->medicine->medicine_name ?? 'غير محدد' }}</td>
                                            <td>{{ $item->medicine->scientific_name ?? 'غير محدد' }}</td>
                                            <td><span class="badge badge-secondary">{{ $item->quantity }}</span></td>
                                            <td>{{ number_format($item->unit_price, 2) }} ليرة سوري</td>
                                            <td>{{ number_format($item->total_price, 2) }} ليرة سوري</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <td colspan="5" class="text-left"><strong>إجمالي الطلبية:</strong></td>
                                        <td><strong>{{ number_format($order->orderItems->sum('total_price'), 2) }} ليرة سوري</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const orderCards = document.querySelectorAll('.all-orders-card');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // إزالة الفئة النشطة من جميع الأزرار
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // إضافة الفئة النشطة للزر المحدد
            this.classList.add('active');

            // تصفية البطاقات
            orderCards.forEach(card => {
                const status = card.getAttribute('data-status');
                if (filter === 'all' || status === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>
@endsection
