@extends('layouts.master')
@section('title', 'قائمة المدفوعات')
@section('css')
<link href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.css" rel="stylesheet" type='text/css'>
<style>
    body { background: #f6f8fa; }
    .breadcrumb-header { margin-bottom: 30px; }
    .payment-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 6px 32px 0 rgba(80, 112, 255, 0.10), 0 1.5px 4px 0 rgba(80, 112, 255, 0.08);
        margin-bottom: 36px;
        padding: 0;
        border: none;
        overflow: hidden;
        transition: box-shadow 0.3s;
        position: relative;
    }
    .payment-card:hover {
        box-shadow: 0 12px 40px 0 rgba(80, 112, 255, 0.18), 0 2px 8px 0 rgba(80, 112, 255, 0.10);
    }
    .payment-card-header {
        background: linear-gradient(90deg, #4e73df 0%, #1cc88a 100%);
        color: #fff;
        padding: 24px 32px 18px 32px;
        display: flex;
        align-items: center;
        gap: 18px;
        border-radius: 18px 18px 0 0;
        box-shadow: 0 2px 8px rgba(80,112,255,0.08);
    }
    .payment-card-header .icon {
        font-size: 2.8em;
        background: #fff;
        color: #4e73df;
        border-radius: 50%;
        padding: 14px 18px;
        box-shadow: 0 2px 8px rgba(80,112,255,0.10);
        margin-left: 10px;
    }
    .payment-card-header .main-title {
        font-size: 1.35em;
        font-weight: 800;
        letter-spacing: 1px;
        margin-bottom: 0;
    }
    .payment-card-header .status-badge {
        margin-right: auto;
        font-size: 1.1em;
        box-shadow: 0 1px 4px rgba(80,112,255,0.10);
    }
    .payment-summary-row {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        background: #f8fafc;
        padding: 18px 32px 10px 32px;
        border-bottom: 1px solid #e3e9f7;
    }
    .payment-summary-item {
        flex: 1 1 180px;
        min-width: 180px;
        color: #444;
        font-size: 1.08em;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }
    .payment-summary-item strong {
        color: #2c3e50;
        font-weight: 700;
        margin-left: 4px;
    }
    .payment-details {
        padding: 18px 32px 18px 32px;
        font-size: 1.07em;
    }
    .payment-details-row {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        margin-bottom: 10px;
    }
    .payment-details-item {
        flex: 1 1 220px;
        min-width: 220px;
        color: #555;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }
    .payment-actions {
        display: flex;
        gap: 12px;
        margin-top: 10px;
        padding: 0 32px 18px 32px;
    }
    .btn-view-invoice {
        background: linear-gradient(90deg, #4e73df 0%, #1cc88a 100%);
        color: #fff;
        border: none;
        padding: 8px 22px;
        border-radius: 25px;
        font-weight: 700;
        font-size: 1.08em;
        transition: all 0.3s ease;
    }
    .btn-view-invoice:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(80, 112, 255, 0.3);
        color: #fff;
    }
    .btn-proof {
        background: #fff;
        color: #4e73df;
        border: 1.5px solid #4e73df;
        padding: 7px 18px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 1.05em;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .btn-proof:hover {
        background: #4e73df;
        color: #fff;
    }
    .proof-thumb {
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(80,112,255,0.12);
        border: 2px solid #e3e9f7;
        transition: transform 0.2s;
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
    .invoice-modal .modal-header {
        background: linear-gradient(90deg, #4e73df 0%, #1cc88a 100%);
        color: #fff;
        border-radius: 12px 12px 0 0;
    }
    .invoice-modal .modal-content {
        border-radius: 12px;
    }
    .invoice-summary-table th, .invoice-summary-table td {
        text-align: center;
        vertical-align: middle;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 26px 9px 20px;
        border-radius: 22px;
        font-weight: 800;
        font-size: 1.13em;
        letter-spacing: 1px;
        box-shadow: 0 2px 12px rgba(80,112,255,0.10), 0 1.5px 4px 0 rgba(80, 112, 255, 0.08);
        border: none;
        transition: background 0.2s, color 0.2s;
        margin-top: 2px;
    }
    .status-accepted {
        background: linear-gradient(90deg, #1cc88a 0%, #36b9cc 100%);
        color: #fff;
    }
    .status-rejected {
        background: linear-gradient(90deg, #e74a3b 0%, #f39c12 100%);
        color: #fff;
    }
    .status-pending {
        background: linear-gradient(90deg, #ff9800 0%, #ffc107 100%);
        color: #fff;
        box-shadow: 0 2px 12px #ffc10755;
    }
    .invoice-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 30px;
        border-radius: 28px;
        font-size: 1.18em;
        font-weight: 900;
        letter-spacing: 1px;
        margin: 10px 0 18px 0;
        box-shadow: 0 2px 12px rgba(80,112,255,0.10), 0 1.5px 4px 0 rgba(80, 112, 255, 0.08);
        border: none;
        transition: background 0.2s, color 0.2s;
    }
    .invoice-status-unpaid {
        background: linear-gradient(90deg, #ff5e62 0%, #ff9966 100%);
        color: #fff;
    }
    .invoice-status-paid {
        background: linear-gradient(90deg, #1cc88a 0%, #36b9cc 100%);
        color: #fff;
    }
    .invoice-status-partially {
        background: linear-gradient(90deg, #ffc107 0%, #ff9800 100%);
        color: #fff;
    }
</style>
@endsection
@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">المدفوعات</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ قائمة المدفوعات</span>
        </div>
    </div>
</div>
<!-- breadcrumb -->
@endsection
@section('content')
<div class="row">
    <div class="col-xl-12">
        @if($payments->count() > 0)
            @foreach($payments as $index => $payment)
                <div class="payment-card">
                    <div class="payment-card-header">
                        <span class="icon"><i class="fas fa-money-check-alt"></i></span>
                        <div style="flex:1;">
                            <span class="main-title d-block">دفعة رقم {{ $index + 1 }}<span style="font-size:0.8em; color:#e3e9f7; font-weight:400;"> &nbsp;|&nbsp; فاتورة رقم {{ $payment->invoice->invoice_number ?? '-' }}</span></span>
                            <span class="d-block mt-2">
                                @php $status = strtolower($payment->status); @endphp
                                @if($status == 'مقبولة' || $status == 'confirmed')
                                    <span class="status-badge status-accepted">
                                        <i class="fas fa-check-circle"></i> مقبولة
                                    </span>
                                @elseif($status == 'مرفوضة' || $status == 'rejected')
                                    <span class="status-badge status-rejected">
                                        <i class="fas fa-times-circle"></i> مرفوضة
                                    </span>
                                @else
                                    <span class="status-badge status-pending">
                                        <i class="fas fa-hourglass-half"></i> معلقة
                                    </span>
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="payment-summary-row">
                        <div class="payment-summary-item"><i class="fas fa-calendar-alt"></i> <strong>تاريخ الفاتورة:</strong> {{ $payment->invoice && $payment->invoice->invoice_date ? \Carbon\Carbon::parse($payment->invoice->invoice_date)->format('Y/m/d') : '-' }}</div>
                        <div class="payment-summary-item"><i class="fas fa-receipt"></i> <strong>رقم الطلب:</strong> {{ $payment->invoice && $payment->invoice->order ? $payment->invoice->order->order_number : '-' }}</div>
                        <div class="payment-summary-item"><i class="fas fa-money-bill-wave"></i> <strong>المبلغ المدفوع:</strong> {{ number_format($payment->paid_amount, 2) }} ر.س</div>
                        <div class="payment-summary-item"><i class="fas fa-credit-card"></i> <strong>طريقة الدفع:</strong> {{ $payment->payment_method ?? '-' }}</div>
                        <div class="payment-summary-item"><i class="fas fa-calendar-check"></i> <strong>تاريخ الدفع:</strong> {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('Y/m/d') : '-' }}</div>
                    </div>
                    <div class="payment-details">
                        <div class="payment-details-row">
                            <div class="payment-details-item"><i class="fas fa-sticky-note"></i> <strong>ملاحظات:</strong> {{ $payment->notes ?? '-' }}</div>
                            <div class="payment-details-item">
                                <i class="fas fa-paperclip"></i> <strong>إثبات الدفع:</strong>
                                @if($payment->payment_proof)
                                    @php
                                        $proofUrl = asset('storage/' . $payment->payment_proof);
                                        $isImage = preg_match('/\.(jpg|jpeg|png|gif|bmp|webp)$/i', $payment->payment_proof);
                                        $isPdf = preg_match('/\.pdf$/i', $payment->payment_proof);
                                    @endphp
                                    <div class="d-inline-block">
                                        @if($isImage)
                                            <a href="#" data-toggle="modal" data-target="#proofModal_{{ $payment->id }}">
                                                <img src="{{ $proofUrl }}" alt="إثبات الدفع" style="width: 48px; height: 48px; object-fit: cover; border-radius: 8px; box-shadow: 0 2px 8px rgba(80,112,255,0.12); border: 2px solid #e3e9f7; transition: transform 0.2s;" class="img-thumbnail proof-thumb">
                                            </a>
                                            <!-- Proof Image Modal -->
                                            <div class="modal fade" id="proofModal_{{ $payment->id }}" tabindex="-1" role="dialog" aria-labelledby="proofModalLabel_{{ $payment->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title" id="proofModalLabel_{{ $payment->id }}">
                                                                <i class="fas fa-paperclip ml-1"></i> إثبات الدفع
                                                            </h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <img src="{{ $proofUrl }}" alt="إثبات الدفع" style="max-width: 100%; max-height: 70vh; border-radius: 12px; box-shadow: 0 4px 24px rgba(80,112,255,0.18);">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($isPdf)
                                            <a href="{{ $proofUrl }}" target="_blank" class="btn btn-proof">
                                                <i class="fas fa-file-pdf ml-1 text-danger"></i> عرض ملف PDF
                                            </a>
                                        @else
                                            <a href="{{ $proofUrl }}" target="_blank" class="btn btn-proof">
                                                <i class="fas fa-file-alt ml-1"></i> تحميل/عرض الملف
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">لا يوجد</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="payment-actions">
                        @if($payment->invoice)
                        <button type="button" class="btn btn-view-invoice" data-toggle="modal" data-target="#invoiceModal_{{ $payment->id }}">
                            <i class="fas fa-eye ml-1"></i>عرض الفاتورة
                        </button>
                        @endif
                    </div>
                </div>

                <!-- Invoice Modal -->
                @if($payment->invoice)
                <div class="modal fade invoice-modal" id="invoiceModal_{{ $payment->id }}" tabindex="-1" role="dialog" aria-labelledby="invoiceModalLabel_{{ $payment->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="invoiceModalLabel_{{ $payment->id }}">
                                    <i class="fas fa-file-invoice ml-1"></i> تفاصيل الفاتورة رقم {{ $payment->invoice->invoice_number }}
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="text-center">
                                    @php
                                        $invStatus = strtolower($payment->invoice->status);
                                    @endphp
                                    @if($invStatus == 'unpaid')
                                        <span class="invoice-status-badge invoice-status-unpaid">
                                            <i class="fas fa-exclamation-circle"></i> غير مدفوعة
                                        </span>
                                    @elseif($invStatus == 'paid')
                                        <span class="invoice-status-badge invoice-status-paid">
                                            <i class="fas fa-check-circle"></i> مدفوعة
                                        </span>
                                    @elseif($invStatus == 'partially')
                                        <span class="invoice-status-badge invoice-status-partially">
                                            <i class="fas fa-hourglass-half"></i> مدفوعة جزئياً
                                        </span>
                                    @else
                                        <span class="invoice-status-badge invoice-status-unpaid">
                                            <i class="fas fa-info-circle"></i> {{ $payment->invoice->status }}
                                        </span>
                                    @endif
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p><strong>رقم الفاتورة:</strong> {{ $payment->invoice->invoice_number }}</p>
                                        <p><strong>تاريخ الفاتورة:</strong> {{ $payment->invoice->invoice_date ? \Carbon\Carbon::parse($payment->invoice->invoice_date)->format('Y/m/d') : '-' }}</p>
                                        <p><strong>تاريخ الاستحقاق:</strong> {{ $payment->invoice->due_date ? \Carbon\Carbon::parse($payment->invoice->due_date)->format('Y/m/d') : '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>رقم الطلب:</strong> {{ $payment->invoice->order->order_number ?? '-' }}</p>
                                        <p><strong>إجمالي الفاتورة:</strong> {{ number_format($payment->invoice->total_amount, 2) }} ر.س</p>
                                        <p><strong>ملاحظات:</strong> {{ $payment->invoice->notes ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered invoice-summary-table">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>#</th>
                                                <th>اسم الدواء</th>
                                                <th>الكمية</th>
                                                <th>سعر الوحدة</th>
                                                <th>الإجمالي</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($payment->invoice->order && $payment->invoice->order->orderItems)
                                                @foreach($payment->invoice->order->orderItems as $idx => $item)
                                                    <tr>
                                                        <td>{{ $idx + 1 }}</td>
                                                        <td>{{ $item->medicine->medicine_name ?? '-' }}</td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td>{{ number_format($item->unit_price, 2) }} ر.س</td>
                                                        <td>{{ number_format($item->total_price, 2) }} ر.س</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr><td colspan="5">لا توجد أصناف مرتبطة</td></tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        @else
            <div class="empty-state">
                <div class="icon">
                    <i class="fas fa-money-check-alt"></i>
                </div>
                <h3>لا توجد مدفوعات حتى الآن</h3>
                <p>لم يتم تسجيل أي مدفوعات بعد. عند إضافة مدفوعات جديدة ستظهر هنا مع تفاصيلها الكاملة.</p>
            </div>
        @endif
    </div>
</div>
@endsection
@section('js')
<!-- Internal Data tables -->
<script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/jszip.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/pdfmake.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/vfs_fonts.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/buttons.html5.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/buttons.print.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{URL::asset('assets/js/table-data.js')}}"></script>
@endsection