@extends('layouts.master')
@section('title')
    قائمة الطلبات الملغية
@stop
@section('css')
<link href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.css" rel="stylesheet" type='text/css'>
<style>
    body { background: #f6f8fa; }
    .canceled-order-card {
        background: linear-gradient(120deg, #f8fafc 60%, #ffeaea 100%);
        border-radius: 18px;
        box-shadow: 0 6px 32px 0 rgba(231, 76, 59, 0.10), 0 1.5px 4px 0 rgba(231, 76, 59, 0.08);
        margin-bottom: 32px;
        padding: 28px 28px 18px 28px;
        transition: box-shadow 0.3s;
        border: none;
        position: relative;
        border-left: 4px solid #e74a3b;
    }
    .canceled-order-card:hover {
        box-shadow: 0 12px 40px 0 rgba(231, 76, 59, 0.18), 0 2px 8px 0 rgba(231, 76, 59, 0.10);
    }
    .canceled-order-card .order-header {
        display: flex;
        align-items: center;
        gap: 18px;
        margin-bottom: 20px;
    }
    .canceled-order-card .order-header .icon {
        font-size: 2.2em;
        color: #e74a3b;
        background: #ffeaea;
        border-radius: 50%;
        padding: 12px 16px;
        box-shadow: 0 2px 8px rgba(231,76,59,0.08);
    }
    .canceled-order-card .order-header .order-title {
        font-size: 1.25em;
        font-weight: 700;
        color: #2c3e50;
    }
    .canceled-order-card .order-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
        font-size: 1.05em;
    }
    .canceled-order-card .order-info div {
        color: #444;
        padding: 8px 0;
    }
    .canceled-order-card .badge-cancelled {
        background: linear-gradient(90deg, #e74a3b 0%, #f39c12 100%);
        color: #fff;
        font-size: 1em;
        padding: 8px 18px;
        border-radius: 16px;
        box-shadow: 0 1px 4px rgba(231,76,59,0.08);
        font-weight: 600;
        letter-spacing: 1px;
    }
    .cancellation-section {
        background: rgba(255,255,255,0.7);
        border-radius: 12px;
        padding: 20px;
        margin-top: 15px;
        border-left: 3px solid #e74a3b;
    }
    .cancellation-reason {
        background: linear-gradient(90deg, #ffeaea, #fff5f5);
        border-radius: 8px;
        padding: 15px;
        margin-top: 10px;
        border: 1px solid #ffcdd2;
    }
    .cancellation-reason h6 {
        color: #e74a3b;
        margin-bottom: 8px;
        font-weight: 600;
    }
    .cancellation-reason p {
        color: #666;
        margin: 0;
        line-height: 1.5;
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
        border-bottom: 2px solid #e74a3b;
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
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: linear-gradient(135deg, #f8fafc 0%, #ffeaea 100%);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(231, 76, 59, 0.10);
        margin: 40px 0;
        border: 1.5px solid #ffeaea;
        position: relative;
        overflow: hidden;
    }
    .empty-state .icon {
        font-size: 4em;
        color: #e74a3b;
        margin-bottom: 20px;
        opacity: 0.8;
        animation: shake 1.5s infinite alternate;
    }
    @keyframes shake {
        0% { transform: rotate(-6deg); }
        100% { transform: rotate(6deg); }
    }
    .empty-state h3 {
        color: #e74a3b;
        font-weight: 700;
        margin-bottom: 15px;
        font-size: 1.5em;
        letter-spacing: 1px;
    }
    .empty-state p {
        color: #b23c2a;
        font-size: 1.1em;
        line-height: 1.6;
        margin-bottom: 30px;
    }
    .empty-state .btn {
        background: linear-gradient(90deg, #e74a3b 0%, #f39c12 100%);
        border: none;
        padding: 12px 30px;
        border-radius: 25px;
        font-weight: 600;
        color: #fff;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(231, 76, 59, 0.10);
    }
    .empty-state .btn:hover {
        transform: translateY(-2px) scale(1.04);
        box-shadow: 0 8px 25px rgba(231, 76, 59, 0.18);
        background: linear-gradient(90deg, #f39c12 0%, #e74a3b 100%);
    }
</style>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الطلبات</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ قائمة الطلبات الملغية</span>
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
    @if($orders->count() == 0)
    <div class="empty-state">
        <div class="icon">
            <i class="fas fa-times-circle"></i>
        </div>
        <h3>لا توجد طلبات ملغية</h3>
        <p>جميع الطلبات في حالة ممتازة ولا يوجد أي إلغاء حالياً.<br>نتمنى لك يوماً سعيداً!</p>
        <a href="{{ route('supplier.orders.index') }}" class="btn">
            <i class="fas fa-arrow-left ml-1"></i>العودة للطلبات الجديدة
        </a>
    </div>
@else
            @foreach($orders as $order)
                <div class="canceled-order-card">
                    <div class="order-header">
                        <span class="icon"><i class="fas fa-times-circle"></i></span>
                        <span class="order-title">طلبية رقم {{ $order->order_number }}</span>
                        <span class="badge badge-cancelled ml-auto">ملغية</span>
                    </div>
                    <div class="order-info">
                        <div><i class="fas fa-store ml-1"></i> <strong>الصيدلية:</strong> صيدلية الهدى</div>
                        <div><i class="fas fa-calendar-alt ml-1"></i> <strong>تاريخ الطلب:</strong> {{ \Carbon\Carbon::parse($order->order_date)->format('Y/m/d') }}</div>
                        <div><i class="fas fa-calendar-times ml-1"></i> <strong>تاريخ الإلغاء:</strong> {{ \Carbon\Carbon::parse($order->updated_at)->format('Y/m/d') }}</div>
                    </div>
                    
                    @if($order->note)
                        <div class="cancellation-section">
                            <h6><i class="fas fa-exclamation-triangle ml-1"></i> سبب الإلغاء</h6>
                            <div class="cancellation-reason">
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
@endsection
