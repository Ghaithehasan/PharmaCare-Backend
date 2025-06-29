@extends('layouts.master')
@section('title')
    الفواتير المدفوعة
@stop
@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
    body { background: #f6f8fa; }
    
    /* Notification Styles */
    .custom-notification {
        position: fixed;
        top: 20px;
        right: -400px;
        width: 380px;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
        z-index: 9999;
        overflow: hidden;
        animation: slideInNotification 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
        border: 2px solid rgba(255, 255, 255, 0.2);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    @keyframes slideInNotification {
        0% {
            right: -400px;
            transform: scale(0.8) rotate(-5deg);
            opacity: 0;
        }
        50% {
            transform: scale(1.05) rotate(2deg);
        }
        100% {
            right: 20px;
            transform: scale(1) rotate(0deg);
            opacity: 1;
        }
    }

    .notification-content {
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        position: relative;
    }

    .notification-icon {
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: pulseIcon 2s infinite;
    }

    .notification-icon i {
        font-size: 24px;
        color: #fff;
        animation: bounceIcon 1s ease-in-out infinite;
    }

    @keyframes pulseIcon {
        0%, 100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
        }
        50% {
            transform: scale(1.05);
            box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
        }
    }

    @keyframes bounceIcon {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-8px);
        }
        60% {
            transform: translateY(-4px);
        }
    }

    .notification-text {
        flex: 1;
        color: #fff;
    }

    .notification-text h4 {
        margin: 0 0 5px 0;
        font-size: 16px;
        font-weight: 700;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .notification-text p {
        margin: 0;
        font-size: 13px;
        opacity: 0.9;
        line-height: 1.4;
    }

    .notification-close {
        width: 25px;
        height: 25px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .notification-close:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.1) rotate(90deg);
    }

    .notification-close i {
        color: #fff;
        font-size: 12px;
    }

    .notification-progress {
        height: 3px;
        background: rgba(255, 255, 255, 0.3);
        animation: progressBar 4s linear forwards;
    }

    @keyframes progressBar {
        0% {
            width: 100%;
        }
        100% {
            width: 0%;
        }
    }

    .custom-notification.hide {
        animation: slideOutNotification 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
    }

    @keyframes slideOutNotification {
        0% {
            right: 20px;
            transform: scale(1) rotate(0deg);
            opacity: 1;
        }
        100% {
            right: -400px;
            transform: scale(0.8) rotate(-5deg);
            opacity: 0;
        }
    }

    /* Shimmer effect */
    .custom-notification::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
        animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
        0% {
            transform: translateX(-100%);
        }
        100% {
            transform: translateX(100%);
        }
    }

    .paid-alert {
        background: linear-gradient(90deg, #d4edda 0%, #b2f7c1 100%);
        color: #155724;
        border-radius: 14px;
        padding: 18px 24px;
        margin-bottom: 32px;
        font-size: 1.15em;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(40,167,69,0.08);
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .all-invoices-card {
        background: linear-gradient(120deg, #f8fafc 60%, #e8f4fd 100%);
        border-radius: 18px;
        box-shadow: 0 8px 32px 0 rgba(80, 112, 255, 0.13), 0 2px 8px 0 rgba(80, 112, 255, 0.10);
        margin-bottom: 36px;
        padding: 32px 32px 22px 32px;
        transition: box-shadow 0.3s, transform 0.2s;
        border: none;
        position: relative;
        border-left: 5px solid #28a745;
    }
    .all-invoices-card:hover {
        box-shadow: 0 16px 48px 0 rgba(80, 112, 255, 0.18), 0 4px 16px 0 rgba(80, 112, 255, 0.13);
        transform: translateY(-4px) scale(1.01);
    }
    .all-invoices-card .invoice-header {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        margin-bottom: 18px;
    }
    .all-invoices-card .invoice-header .icon {
        font-size: 2.5em;
        color: #28a745;
        background: #eafaf1;
        border-radius: 50%;
        padding: 14px 18px;
        box-shadow: 0 2px 8px rgba(40,167,69,0.10);
        margin-bottom: 6px;
    }
    .all-invoices-card .invoice-header .invoice-title {
        font-size: 1.35em;
        font-weight: 800;
        color: #2c3e50;
        letter-spacing: 1px;
        text-align: center;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .all-invoices-card .invoice-header .status-badge {
        margin-top: 8px;
    }
    .badge-paid {
        background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
        color: #fff;
        font-size: 1em;
        padding: 8px 18px;
        border-radius: 16px;
        box-shadow: 0 1px 4px rgba(40,167,69,0.08);
        font-weight: 600;
        letter-spacing: 1px;
    }
    .invoice-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        margin-bottom: 18px;
        font-size: 1.08em;
    }
    .all-invoices-card .invoice-info div {
        color: #444;
        padding: 8px 0;
    }
    .invoice-details {
        background: rgba(255,255,255,0.8);
        border-radius: 12px;
        padding: 18px;
        margin-top: 10px;
        box-shadow: 0 2px 8px rgba(80,112,255,0.06);
    }
    .invoice-details h6 {
        color: #2c3e50;
        margin-bottom: 12px;
        font-weight: 700;
        border-bottom: 2px solid #28a745;
        padding-bottom: 8px;
        font-size: 1.08em;
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
    .actions {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 10px;
    }
    .btn-outline-primary {
        border-color: #28a745;
        color: #28a745;
        font-weight: 600;
        background: #f8f9fa;
        transition: all 0.2s;
    }
    .btn-outline-primary:hover {
        background: #28a745;
        color: #fff;
        border-color: #28a745;
        box-shadow: 0 2px 8px rgba(40,167,69,0.13);
    }
    .btn-outline-info {
        border-color: #36d1dc;
        color: #36d1dc;
        font-weight: 600;
        background: #f8f9fa;
        transition: all 0.3s ease;
    }
    .btn-outline-info:hover {
        background: #28a745 !important;
        color: #fff !important;
        border-color: #28a745 !important;
        box-shadow: 0 4px 12px rgba(40,167,69,0.25) !important;
        transform: translateY(-2px);
    }
    .btn-outline-secondary {
        border-color: #6c757d;
        color: #6c757d;
        font-weight: 600;
        background: #f8f9fa;
        transition: all 0.2s;
    }
    .btn-outline-secondary:hover {
        background: #6c757d;
        color: #fff;
        border-color: #6c757d;
        box-shadow: 0 2px 8px rgba(108,117,125,0.13);
    }
    /* تأكيد إضافي لزر العرض */
    .actions .btn-outline-info:hover {
        background: #28a745 !important;
        color: #fff !important;
        border-color: #28a745 !important;
        box-shadow: 0 4px 12px rgba(40,167,69,0.25) !important;
        transform: translateY(-2px);
    }
    
    /* تأثير إضافي للزر عند التمرير */
    .actions a.btn-outline-info:hover {
        background: #28a745 !important;
        color: #fff !important;
        border-color: #28a745 !important;
    }
    
    /* كلاس مخصص لزر العرض */
    .view-btn:hover {
        background: #28a745 !important;
        color: #fff !important;
        border-color: #28a745 !important;
        box-shadow: 0 4px 12px rgba(40,167,69,0.25) !important;
        transform: translateY(-2px);
    }
    
    @media (max-width: 600px) {
        .all-invoices-card {
            padding: 16px 6px 12px 6px;
        }
        .invoice-details {
            padding: 8px;
        }
        .invoice-header .invoice-title {
            font-size: 1em;
        }
        .custom-notification {
            width: 320px;
            right: 10px;
        }
    }
</style>
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الفواتير</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ الفواتير المدفوعة</span>
            </div>
        </div>
    </div>
@endsection
@section('content')

<div class="row">
    <div class="col-xl-12">
        @if(session()->has('add_archive'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    showArchiveNotification();
                });

                function showArchiveNotification() {
                    // إنشاء العنصر الرئيسي للإشعار
                    const notification = document.createElement('div');
                    notification.className = 'custom-notification';
                    notification.innerHTML = `
                        <div class="notification-content">
                            <div class="notification-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="notification-text">
                                <h4>تم الأرشفة بنجاح! 🎉</h4>
                                <p>تم أرشفة الفاتورة بنجاح في النظام</p>
                            </div>
                            <div class="notification-close">
                                <i class="fas fa-times"></i>
                            </div>
                        </div>
                        <div class="notification-progress"></div>
                    `;

                    // إضافة الإشعار للصفحة
                    document.body.appendChild(notification);

                    // إغلاق الإشعار عند النقر على زر الإغلاق
                    const closeBtn = notification.querySelector('.notification-close');
                    closeBtn.addEventListener('click', () => {
                        hideNotification(notification);
                    });

                    // إغلاق تلقائي بعد 4 ثوان
                    setTimeout(() => {
                        if (document.body.contains(notification)) {
                            hideNotification(notification);
                        }
                    }, 4000);

                    // تأثيرات إضافية عند الظهور
                    setTimeout(() => {
                        notification.style.transform = 'scale(1.02)';
                        setTimeout(() => {
                            notification.style.transform = 'scale(1)';
                        }, 200);
                    }, 600);
                }

                function hideNotification(notification) {
                    notification.classList.add('hide');
                    setTimeout(() => {
                        if (document.body.contains(notification)) {
                            document.body.removeChild(notification);
                        }
                    }, 500);
                }
            </script>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle ml-1"></i>
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        
        @if($invoices->count() > 0)
        <div class="paid-alert">
            <i class="fas fa-check-circle fa-lg"></i>
            <span>تهانينا! جميع الفواتير في هذه الصفحة تم دفعها بنجاح من قبل صيدلية الهدى.</span>
        </div>
        @endif
        @if($invoices->count() == 0)
            <div class="text-center py-5">
                <i class="fas fa-file-invoice" style="font-size: 4em; color: #28a745;"></i>
                <h4 class="mt-3 text-muted">لا توجد فواتير مدفوعة</h4>
                <p class="text-muted">لم يتم دفع أي فاتورة بعد</p>
            </div>
        @else
            @foreach($invoices as $invoice)
                <div class="all-invoices-card">
                    <div class="invoice-header">
                        <span class="icon"><i class="fas fa-file-invoice-dollar"></i></span>
                        <span class="invoice-title"><i class="fas fa-hashtag ml-1"></i> فاتورة رقم {{ $invoice->invoice_number }}</span>
                        <span class="badge badge-paid status-badge">مدفوعة</span>
                    </div>
                    <div class="invoice-info">
                        <div><i class="fas fa-hashtag ml-1"></i> <strong>رقم الطلبية:</strong> {{ $invoice->order->order_number ?? '-' }}</div>
                        <div><i class="fas fa-user ml-1"></i> <strong>المورد:</strong> {{ $invoice->order->supplier->contact_person_name ?? '-' }}</div>
                        <div><i class="fas fa-calendar-alt ml-1"></i> <strong>تاريخ الفاتورة:</strong> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('Y/m/d') }}</div>
                        <div><i class="fas fa-calendar-check ml-1"></i> <strong>تاريخ الاستحقاق:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('Y/m/d') }}</div>
                        <div><i class="fas fa-money-bill ml-1"></i> <strong>الإجمالي:</strong> {{ number_format($invoice->total_amount, 2) }} ليرة سوري</div>
                    </div>
                    <div class="invoice-details">
                        <h6><i class="fas fa-list-ul ml-1"></i> ملخص الطلبية</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>المنتج</th>
                                        <th>الكمية</th>
                                        <th>سعر الوحدة</th>
                                        <th>الإجمالي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->order->orderItems as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->medicine->medicine_name ?? 'غير محدد' }}</td>
                                            <td><span class="badge badge-secondary">{{ $item->quantity }}</span></td>
                                            <td>{{ number_format($item->unit_price, 2) }} ل.س</td>
                                            <td>{{ number_format($item->total_price, 2) }} ل.س</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="actions">
                            <a href="{{ route('invoices.download', $invoice->id) }}" class="btn btn-sm btn-outline-primary" title="تحميل الفاتورة"><i class="fas fa-download"></i> تحميل</a>
                            <a href="{{ route('supplier.invoices.show-pdf', $invoice->id) }}" class="btn btn-sm btn-outline-info view-btn" title="عرض الفاتورة" target="_blank"><i class="fas fa-eye"></i> عرض</a>
                            <form action="{{ route('supplier.invoices.archive', $invoice->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="أرشف الفاتورة" onclick="return confirm('هل أنت متأكد من أرشفة هذه الفاتورة؟')">
                                    <i class="fas fa-archive"></i> أرشف
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
        <div class="d-flex justify-content-center mt-3">
            {{ $invoices->links() }}
        </div>
    </div>
</div>
@endsection

@section('js')
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
<script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>
@endsection
