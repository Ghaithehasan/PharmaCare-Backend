@extends('layouts.master')
@section('title')
    قائمة الطلبات المقبولة
@stop
@section('css')
<link href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.css" rel="stylesheet" type='text/css'>
<link
  href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.css"
  rel="stylesheet"  type='text/css'>
    <!-- Internal Data table css -->
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <!--Internal   Notify -->
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
<style>
    body { background: #f6f8fa; }
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        margin: -20px -20px 30px -20px;
        padding: 30px 20px;
        color: white;
        border-radius: 0 0 15px 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    .page-header h4 {
        margin: 0;
        font-weight: 700;
        letter-spacing: 1px;
        font-size: 1.5em;
    }
    .modern-order-card {
        background: linear-gradient(120deg, #f8fafc 60%, #e3e9f7 100%);
        border-radius: 18px;
        box-shadow: 0 6px 32px 0 rgba(80, 112, 255, 0.10), 0 1.5px 4px 0 rgba(80, 112, 255, 0.08);
        margin-bottom: 32px;
        padding: 28px 28px 18px 28px;
        transition: box-shadow 0.3s;
        border: none;
        position: relative;
    }
    .modern-order-card:hover {
        box-shadow: 0 12px 40px 0 rgba(80, 112, 255, 0.18), 0 2px 8px 0 rgba(80, 112, 255, 0.10);
    }
    .modern-order-card .order-header {
        display: flex;
        align-items: center;
        gap: 18px;
        margin-bottom: 20px;
    }
    .modern-order-card .order-header .icon {
        font-size: 2.2em;
        color: #4e73df;
        background: #e3e9f7;
        border-radius: 50%;
        padding: 12px 16px;
        box-shadow: 0 2px 8px rgba(80,112,255,0.08);
    }
    .modern-order-card .order-header .order-title {
        font-size: 1.25em;
        font-weight: 700;
        color: #2c3e50;
    }
    .modern-order-card .order-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
        font-size: 1.05em;
    }
    .modern-order-card .order-info div {
        color: #444;
        padding: 8px 0;
    }
    .modern-order-card .badge-confirmed {
        background: linear-gradient(90deg, #4e73df 0%, #1cc88a 100%);
        color: #fff;
        font-size: 1em;
        padding: 8px 18px;
        border-radius: 16px;
        box-shadow: 0 1px 4px rgba(80,112,255,0.08);
        font-weight: 600;
        letter-spacing: 1px;
    }
    .timeline-section {
        background: rgba(255,255,255,0.7);
        border-radius: 12px;
        padding: 20px;
        margin-top: 15px;
    }
    .timeline-labels {
        display: flex;
        justify-content: space-between;
        font-size: 0.92em;
        color: #6c757d;
        margin-bottom: 12px;
        font-weight: 500;
    }
    .progress-timeline {
        background: #e9ecef;
        border-radius: 20px;
        height: 20px;
        width: 100%;
        overflow: hidden;
        margin-bottom: 12px;
        box-shadow: 0 1px 4px rgba(80,112,255,0.08);
        position: relative;
    }
    .progress-timeline-bar {
        background: linear-gradient(90deg, #4e73df, #1cc88a, #36b9cc);
        height: 100%;
        width: 0;
        border-radius: 20px;
        transition: width 1.7s cubic-bezier(.4,2,.6,1);
        position: relative;
        animation: gradientMove 2.5s linear infinite alternate;
    }
    @keyframes gradientMove {
        0% { filter: brightness(1); }
        100% { filter: brightness(1.15); }
    }
    .progress-timeline-bar::after {
        content: '';
        display: block;
        position: absolute;
        right: 0;
        top: 0;
        width: 16px;
        height: 100%;
        background: radial-gradient(circle, #fff 40%, #1cc88a22 100%);
        opacity: 0.7;
        animation: pulse 1.2s infinite alternate;
    }
    @keyframes pulse {
        0% { opacity: 0.7; }
        100% { opacity: 1; }
    }
    .timeline-status {
        font-size: 1em;
        color: #4e73df;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        justify-content: center;
    }
    .days-left {
        background: linear-gradient(90deg, #1cc88a, #36b9cc);
        color: white;
        padding: 6px 12px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9em;
    }
    .days-overdue {
        background: linear-gradient(90deg, #e74a3b, #f39c12);
        color: white;
        padding: 6px 12px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9em;
    }
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: linear-gradient(135deg, #f8fafc 0%, #e3e9f7 100%);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(80, 112, 255, 0.08);
        margin: 40px 0;
    }
    .empty-state .icon {
        font-size: 4em;
        color: #4e73df;
        margin-bottom: 20px;
        opacity: 0.7;
    }
    .empty-state h3 {
        color: #2c3e50;
        font-weight: 700;
        margin-bottom: 15px;
        font-size: 1.5em;
    }
    .empty-state p {
        color: #6c757d;
        font-size: 1.1em;
        line-height: 1.6;
        margin-bottom: 30px;
    }
    .empty-state .btn {
        background: linear-gradient(90deg, #4e73df 0%, #1cc88a 100%);
        border: none;
        padding: 12px 30px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .empty-state .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(80, 112, 255, 0.3);
    }
</style>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الطلبات</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ قائمة الطلبات المقبولة</span>
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
@if (session()->has('update_expiry'))
        <script>
            window.onload = function() {
                notif({
                    msg: "{{ session('update_expiry') }}",
                    type: "success"
                })
            }
        </script>
    @endif


<div class="row">

    <div class="col-xl-12">

        @if($orders->count() > 0)
            @foreach($orders as $order)
                <div class="modern-order-card">
                    <div class="order-header">
                        <span class="icon"><i class="fas fa-clipboard-check"></i></span>
                        <span class="order-title">طلبية رقم {{ $order->order_number }}</span>
                        <span class="badge badge-confirmed ml-auto">مقبول</span>
                    </div>
                    <div class="order-info">
                        <div><i class="fas fa-store ml-1"></i> <strong>الصيدلية:</strong> صيدلية الهدى</div>
                        <div><i class="fas fa-calendar-alt ml-1"></i> <strong>تاريخ الطلب:</strong> {{ \Carbon\Carbon::parse($order->order_date)->format('Y/m/d') }}</div>
                        <div><i class="fas fa-truck ml-1"></i> <strong>تاريخ التسليم المتوقع:</strong> {{ $order->delevery_date ? \Carbon\Carbon::parse($order->delevery_date)->format('Y/m/d') : 'غير محدد' }}</div>
                    </div>
                    @if($order->delevery_date)
                        @php
                            $orderDate = \Carbon\Carbon::parse($order->order_date);
                            $deliveryDate = \Carbon\Carbon::parse($order->delevery_date);
                            $now = \Carbon\Carbon::now();

                            // حساب الأيام المتبقية
                            $daysLeft = round($now->diffInDays($deliveryDate, false));

                            // حساب النسبة المئوية
                            $totalDays = $orderDate->diffInDays($deliveryDate);
                            $passedDays = $orderDate->diffInDays($now);

                            if($totalDays <= 0) {
                                $progress = 100;
                            } else {
                                if($passedDays <= 0) {
                                    $progress = 0;
                                } elseif($passedDays >= $totalDays) {
                                    $progress = 100;
                                } else {
                                    $progress = round(($passedDays / $totalDays) * 100);
                                }
                            }
                        @endphp
                        <div class="timeline-section">
                            <div class="timeline-labels">
                                <span><i class="fas fa-calendar-day"></i> {{ $orderDate->format('Y/m/d') }}</span>
                                <span><i class="fas fa-calendar-check"></i> {{ $deliveryDate->format('Y/m/d') }}</span>
                            </div>
                            <div class="progress-timeline">
                                <div class="progress-timeline-bar" id="progress_bar_{{ $order->id }}" style="width:0%"></div>
                            </div>
                            <div class="timeline-status">
                                <i class="fas fa-hourglass-half"></i>
                                <span>{{ $progress }}% من المدة حتى التسليم</span>
                                @if($daysLeft > 0)
                                    <span class="days-left">متبقي {{ $daysLeft }} يوم{{ $daysLeft > 1 ? 'اً' : '' }}</span>
                                @elseif($daysLeft == 0)
                                    <span class="days-left" style="background: linear-gradient(90deg, #e74a3b, #f39c12);">اليوم موعد التسليم</span>
                                @else
                                    <span class="days-overdue">متأخر {{ abs($daysLeft) }} يوم{{ abs($daysLeft) > 1 ? 'اً' : '' }}</span>
                                @endif
                            </div>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                setTimeout(function() {
                                    var bar = document.getElementById('progress_bar_{{ $order->id }}');
                                    if(bar) bar.style.width = '{{ $progress }}%';
                                }, 300);
                            });
                        </script>
                    @else
                        <div class="timeline-section">
                            <div class="text-muted text-center">
                                <i class="fas fa-info-circle"></i> تاريخ التسليم غير محدد
                            </div>
                        </div>
                    @endif
                    <!-- إضافة جدول الأصناف مع إدخال تاريخ الصلاحية -->
                    @if($order->orderItems && $order->orderItems->count())
                        <div class="table-responsive mt-3">
                            <form method="POST" action="{{ route('supplier.orders.update_expiry_bulk') }}" id="expiry_form_{{ $order->id }}">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>اسم الدواء</th>
                                            <th>الكمية</th>
                                            <th>تاريخ الصلاحية</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->orderItems as $item)
                                            <tr>
                                                <td>{{ $item->medicine->medicine_name ?? '-' }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>
                                                    <input type="date" name="expiry_dates[{{ $item->id }}]" class="form-control" value="{{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('Y-m-d') : '' }}" required>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="text-center mt-3">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-save ml-1"></i> حفظ تواريخ الصلاحية
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <div class="icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <h3>لا توجد طلبات مقبولة حالياً</h3>
                <p>لم يتم قبول أي طلبات بعد. عندما يتم قبول طلبات جديدة، ستظهر هنا مع تفاصيلها الكاملة وخط زمني للتسليم.</p>
                <a href="{{ route('supplier.orders.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left ml-1"></i>العودة للطلبات الجديدة
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
@section('js')
    <!-- Internal Data tables -->
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/pdfmake.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/vfs_fonts.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js') }}"></script>
    <!--Internal  Datatable js -->
    <script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
    <!--Internal  Notify js -->
    <script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>

    <script>
        // تحديث حالة المنتج

    </script>
@endsection