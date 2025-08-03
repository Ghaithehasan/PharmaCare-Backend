@extends('layouts.master')
@section('title')
    الفواتير غير المدفوعة
@stop
@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
    body { background: #f6f8fa; }
    .unpaid-alert {
        background: linear-gradient(90deg, #fff3cd 0%, #ffe082 100%);
        color: #856404;
        border-radius: 14px;
        padding: 18px 24px;
        margin-bottom: 32px;
        font-size: 1.15em;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(255,193,7,0.08);
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    .unpaid-alert .text-danger {
        color: #dc3545 !important;
        font-size: 1.1em;
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
        border-left: 5px solid #ffc107;
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
        color: #ffc107;
        background: #fff8e1;
        border-radius: 50%;
        padding: 14px 18px;
        box-shadow: 0 2px 8px rgba(255,193,7,0.10);
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
    .badge-unpaid {
        background: linear-gradient(90deg, #ffc107 0%, #ff9800 100%);
        color: #fff;
        font-size: 1em;
        padding: 8px 18px;
        border-radius: 16px;
        box-shadow: 0 1px 4px rgba(255,193,7,0.08);
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
        border-bottom: 2px solid #ffc107;
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
        border-color: #ffc107;
        color: #ffc107;
        font-weight: 600;
        background: #f8f9fa;
        transition: all 0.2s;
    }
    .btn-outline-primary:hover {
        background: #ffc107;
        color: #fff;
        border-color: #ffc107;
        box-shadow: 0 2px 8px rgba(255,193,7,0.13);
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
    /* تأكيد إضافي لزر العرض */
    .actions .btn-outline-info:hover {
        background: #28a745 !important;
        color: #fff !important;
        border-color: #28a745 !important;
        box-shadow: 0 4px 12px rgba(40,167,69,0.25) !important;
        transform: translateY(-2px);
    }
    .invoice-info .text-danger {
        background: linear-gradient(90deg, #dc3545 0%, #c82333 100%);
        color: white !important;
        padding: 4px 8px;
        border-radius: 8px;
        font-weight: 700;
        box-shadow: 0 1px 3px rgba(220,53,69,0.2);
    }
    .invoice-info div {
        transition: all 0.2s ease;
    }
    .invoice-info div:hover {
        transform: translateX(2px);
    }
    .remaining-amount-badge {
        background: linear-gradient(90deg, #dc3545 0%, #c82333 100%);
        color: white;
        padding: 4px 10px;
        border-radius: 8px;
        font-weight: 700;
        box-shadow: 0 2px 4px rgba(220,53,69,0.3);
        display: inline-block;
        margin-right: 5px;
        transition: all 0.2s ease;
    }
    .remaining-amount-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 6px rgba(220,53,69,0.4);
    }
    .badge-success {
        background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
        color: white;
        font-weight: 600;
    }
    .badge-danger {
        background: linear-gradient(90deg, #dc3545 0%, #c82333 100%);
        color: white;
        font-weight: 600;
    }
    .badge-warning {
        background: linear-gradient(90deg, #ffc107 0%, #ff9800 100%);
        color: white;
        font-weight: 600;
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
    }
</style>
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الفواتير</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ الفواتير غير المدفوعة</span>
            </div>
        </div>
    </div>
@endsection
@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="unpaid-alert">
            <i class="fas fa-exclamation-triangle fa-lg"></i>
            <div>
                <div><strong>تنبيه:</strong> لديك فواتير غير مدفوعة من الصيدلية.</div>
                <div class="mt-2">
                    <strong>إجمالي المبالغ المستحقة:</strong>
                    <span class="remaining-amount-badge" style="margin-right: 0;">
                        {{ number_format($invoices->sum('total_amount'), 2) }} ليرة سوري
                    </span>
                </div>
                <div class="mt-1">يرجى متابعة التحصيل والتواصل مع صيدلية الهدى في حال التأخير.</div>
            </div>
        </div>
        @if($invoices->count() == 0)
            <div class="text-center py-5">
                <i class="fas fa-file-invoice" style="font-size: 4em; color: #ffc107;"></i>
                <h4 class="mt-3 text-muted">لا توجد فواتير غير مدفوعة</h4>
                <p class="text-muted">جميع فواتيرك مدفوعة أو لا توجد فواتير بعد</p>
            </div>
        @else
            @foreach($invoices as $invoice)
                <div class="all-invoices-card">
                    <div class="invoice-header">
                        <span class="icon"><i class="fas fa-file-invoice-dollar"></i></span>
                        <span class="invoice-title"><i class="fas fa-hashtag ml-1"></i> فاتورة رقم {{ $invoice->invoice_number }}</span>
                        <span class="badge badge-unpaid status-badge">غير مدفوعة</span>
                    </div>
                    <div class="invoice-info">
                        <div><i class="fas fa-hashtag ml-1"></i> <strong>رقم الطلبية:</strong> {{ $invoice->order->order_number ?? '-' }}</div>
                        <div><i class="fas fa-user ml-1"></i> <strong>المورد:</strong> {{ $invoice->order->supplier->contact_person_name ?? '-' }}</div>
                        <div><i class="fas fa-calendar-alt ml-1"></i> <strong>تاريخ الفاتورة:</strong> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('Y/m/d') }}</div>
                        <div><i class="fas fa-calendar-check ml-1"></i> <strong>تاريخ الاستحقاق:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('Y/m/d') }}</div>
                        <div><i class="fas fa-money-bill ml-1"></i> <strong>الإجمالي:</strong> {{ number_format($invoice->total_amount, 2) }} ليرة سوري</div>
                        <div><i class="fas fa-exclamation-triangle ml-1"></i> <strong>المبلغ المتبقي:</strong> <span class="remaining-amount-badge">{{ number_format($invoice->total_amount, 2) }} ليرة سوري</span></div>
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
                        @if($invoice->payments && $invoice->payments->count() > 0)
                            <div class="mt-3">
                                <h6 class="text-info"><i class="fas fa-history ml-1"></i> جميع المدفوعات</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-info">
                                            <tr>
                                                <th>التاريخ</th>
                                                <th>المبلغ</th>
                                                <th>طريقة الدفع</th>
                                                <th>الحالة</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($invoice->payments as $payment)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y/m/d') }}</td>
                                                    <td class="font-weight-bold">{{ number_format($payment->paid_amount, 2) }} ل.س</td>
                                                    <td>{{ $payment->payment_method }}</td>
                                                    <td>
                                                        @if($payment->status == 'confirmed')
                                                            <span class="badge badge-success">مؤكد</span>
                                                        @elseif($payment->status == 'rejected')
                                                            <span class="badge badge-danger">مرفوض</span>
                                                        @elseif($payment->status == 'pending')
                                                            <span class="badge badge-warning">معلق</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                        <div class="actions">
                            <a href="{{ route('invoices.download', $invoice->id) }}" class="btn btn-sm btn-outline-primary" title="تحميل الفاتورة"><i class="fas fa-download"></i> تحميل</a>
                            <a href="{{ route('supplier.invoices.show-pdf', $invoice->id) }}" class="btn btn-sm btn-outline-info" title="عرض الفاتورة" target="_blank"><i class="fas fa-eye"></i> عرض</a>
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
