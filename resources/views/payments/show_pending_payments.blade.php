@extends('layouts.master')
@section('title')
    قائمة المدفوعات المعلقة
@stop
@section('css')
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
        .modal-xl {
            max-width: 1200px;
        }
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .card-header {
            border-radius: 8px 8px 0 0 !important;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0,123,255,0.05);
        }
        .quantity-input, .price-input {
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        .quantity-input:focus, .price-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }
        .item-total {
            font-size: 1.1em;
            transition: all 0.3s ease;
        }
        .product-row:hover .item-total {
            transform: scale(1.05);
        }
        .alert-info {
            border-left: 4px solid #17a2b8;
        }
        .alert-info ul {
            padding-right: 20px;
        }
        .alert-info li {
            margin-bottom: 5px;
        }
        .badge {
            font-size: 0.85em;
            padding: 6px 10px;
        }
        .form-control-sm {
            height: 35px;
            font-size: 0.875rem;
        }
        .modal-footer {
            border-top: 2px solid #e9ecef;
            padding: 15px 20px;
        }
        .btn {
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .text-muted {
            font-size: 0.9em;
        }
        .font-weight-bold {
            font-weight: 600 !important;
        }
        .proof-thumb {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .proof-thumb:hover {
            transform: scale(1.2);
        }
        
        /* تنسيق المودال الجديد */
        .modal-accept {
            border-left: 4px solid #28a745;
        }
        .modal-reject {
            border-left: 4px solid #dc3545;
        }
        .modal-header-accept {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 8px 8px 0 0;
        }
        .modal-header-reject {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            color: white;
            border-radius: 8px 8px 0 0;
        }
        .form-group label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }
        .btn-accept {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            font-weight: 600;
        }
        .btn-reject {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            border: none;
            color: white;
            font-weight: 600;
        }
        .btn-accept:hover, .btn-reject:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
        }
        .payment-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .payment-info h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .payment-info p {
            margin-bottom: 5px;
            color: #6c757d;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
            font-weight: bold;
        }
        /* أنماط المودال */
        .modal-accept .modal-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 8px 8px 0 0;
        }
        .modal-reject .modal-header {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            color: white;
            border-radius: 8px 8px 0 0;
        }
        .btn-accept {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            font-weight: 600;
        }
        .btn-accept:hover {
            background: linear-gradient(135deg, #218838 0%, #1ea085 100%);
            color: white;
            transform: translateY(-2px);
        }
        .btn-reject {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            border: none;
            color: white;
            font-weight: 600;
        }
        .btn-reject:hover {
            background: linear-gradient(135deg, #c82333 0%, #e55a00 100%);
            color: white;
            transform: translateY(-2px);
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        .payment-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
            margin-bottom: 20px;
        }
        .payment-info h6 {
            color: #007bff;
            margin-bottom: 15px;
        }
        .badge-lg {
            font-size: 1.1em;
            padding: 8px 15px;
        }
        .char-counter {
            margin-top: 5px;
        }
        .card-border-primary {
            border-color: #007bff !important;
        }
        .card-border-info {
            border-color: #17a2b8 !important;
        }
    </style>
@endsection
@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="my-auto">
						<div class="d-flex">
                <h4 class="content-title mb-0 my-auto">المدفوعات</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ قائمة
                    المدفوعات المعلقة</span>
            </div>
        </div>

    </div>
    <!-- breadcrumb -->
@endsection
@section('content')

    @if (session()->has('payment_updated'))
        <script>
            window.onload = function() {
                notif({
                    msg: "{{ session('payment_updated') }}",
                    type: "success"
                })
            }
        </script>
    @endif

    @if (session()->has('error'))
        <script>
            window.onload = function() {
                notif({
                    msg: "{{ session('error') }}",
                    type: "error"
                })
            }
        </script>
    @endif

    <!-- row -->
    <div class="row">
        <!--div-->
        <div class="col-xl-12">
            <div class="card mg-b-20">
                <div class="card-header pb-0">
                    @if($payments_pending->count() > 0)
                        <div class="alert alert-info" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle ml-2"></i>
                                <div>
                                    <strong>تنبيه للمورد:</strong> لديك {{ $payments_pending->count() }} مدفوعات معلقة في انتظار التأكيد من الصيدلية. 
                                    يرجى متابعة حالة هذه المدفوعات والتواصل مع الصيدلية إذا لزم الأمر.
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    @if($payments_pending->count() > 0)
                        <div class="table-responsive">
                            <table id="example1" class="table key-buttons text-md-nowrap" data-page-length='50'style="text-align: center">
                                <thead>
                                    <tr>
                                        <th class="border-bottom-0">#</th>
                                        <th class="border-bottom-0">رقم الفاتورة</th>
                                        <th class="border-bottom-0">رقم الطلب</th>
                                        <th class="border-bottom-0">المبلغ المدفوع</th>
                                        <th class="border-bottom-0">طريقة الدفع</th>
                                        <th class="border-bottom-0">تاريخ الدفع</th>
                                        <th class="border-bottom-0">إثبات الدفع</th>
                                        <th class="border-bottom-0">حالة المدفوعة</th>
                                        <th class="border-bottom-0">العمليات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i = 0;
                                    @endphp
                                    @foreach ($payments_pending as $payment)
                                        @php
                                        $i++
                                        @endphp
                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td>{{ $payment->invoice->invoice_number ?? '-' }}</td>
                                            <td>{{ $payment->invoice && $payment->invoice->order ? $payment->invoice->order->order_number : '-' }}</td>
                                            <td>
                                                <span class="badge badge-success">
                                                    <i class="fas fa-money-bill-wave ml-1"></i>
                                                    {{ number_format($payment->paid_amount, 2) }} ر.س
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">
                                                    <i class="fas fa-credit-card ml-1"></i>
                                                    {{ $payment->payment_method ?? '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-primary">
                                                    <i class="fas fa-calendar-check ml-1"></i>
                                                    {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('Y/m/d') : '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($payment->payment_proof)
                                                    @php
                                                        $proofUrl = asset('storage/' . $payment->payment_proof);
                                                        $isImage = preg_match('/\.(jpg|jpeg|png|gif|bmp|webp)$/i', $payment->payment_proof);
                                                        $isPdf = preg_match('/\.pdf$/i', $payment->payment_proof);
                                                    @endphp
                                                    @if($isImage)
                                                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#proofModal_{{ $payment->id }}">
                                                            <i class="fas fa-image ml-1"></i>
                                                            عرض الصورة
                                                        </button>
                                                    @elseif($isPdf)
                                                        <a href="{{ $proofUrl }}" target="_blank" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-file-pdf ml-1"></i>
                                                            عرض PDF
                                                        </a>
                                                    @else
                                                        <a href="{{ $proofUrl }}" target="_blank" class="btn btn-sm btn-secondary">
                                                            <i class="fas fa-file ml-1"></i>
                                                            عرض الملف
                                                        </a>
                                                    @endif
                                                @else
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-times ml-1"></i>
                                                        لا يوجد
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock ml-1"></i>
                                                    معلقة
                                                </span>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button aria-expanded="false" aria-haspopup="true"
                                                        class="btn ripple btn-primary btn-sm" data-toggle="dropdown"
                                                        type="button">العمليات<i class="fas fa-caret-down ml-1"></i></button>
                                                    <div class="dropdown-menu tx-13">
                                                            <a class="dropdown-item text-success" href="#" data-toggle="modal" data-target="#acceptPaymentModal_{{ $payment->id }}">
                                                                <i class="fas fa-check-double"></i> قبول المدفوعة
                                                            </a>
                                                            <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#rejectPaymentModal_{{ $payment->id }}">
                                                                <i class="fas fa-times-circle"></i> رفض المدفوعة
                                                            </a>
                                                            @if($payment->invoice)
                                                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#invoiceModal_{{ $payment->id }}">
                                                                    <i class="fas fa-eye"></i> عرض الفاتورة
                                                                </a>
                                                            @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="d-flex flex-column align-items-center justify-content-center py-5" style="min-height: 400px;">
                            <div class="mb-4">
                                <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="60" cy="60" r="56" fill="#f8f9fa" stroke="#28a745" stroke-width="4"/>
                                    <path d="M40 65L55 80L80 50" stroke="#28a745" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="60" cy="60" r="50" fill="none" stroke="#e9ecef" stroke-width="2"/>
                                </svg>
                            </div>
                            <h3 class="text-success font-weight-bold mb-3">لا توجد مدفوعات معلقة 🎉</h3>
                            <p class="text-muted mb-2" style="font-size:1.1em;">جميع مدفوعاتك تم تأكيدها أو لا توجد مدفوعات جديدة في الوقت الحالي.</p>
                            <p class="text-muted">تابع لوحة التحكم لمتابعة أي تحديثات جديدة.</p>
                            <a href="{{ route('supplier.payments.all_payments') }}" class="btn btn-outline-success mt-3">
                                <i class="fas fa-list"></i> عرض جميع المدفوعات
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Proof Image Modals -->
    @foreach($payments_pending as $payment)
        @if($payment->payment_proof && preg_match('/\.(jpg|jpeg|png|gif|bmp|webp)$/i', $payment->payment_proof))
            <div class="modal fade" id="proofModal_{{ $payment->id }}" tabindex="-1" role="dialog" aria-labelledby="proofModalLabel_{{ $payment->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="proofModalLabel_{{ $payment->id }}">
                                <i class="fas fa-paperclip ml-1"></i> إثبات الدفع
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="{{ asset('storage/' . $payment->payment_proof) }}" alt="إثبات الدفع" style="max-width: 100%; max-height: 70vh; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.2);">
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <!-- Accept Payment Modals -->
    @foreach($payments_pending as $payment)
        <div class="modal fade" id="acceptPaymentModal_{{ $payment->id }}" tabindex="-1" role="dialog" aria-labelledby="acceptPaymentModalLabel_{{ $payment->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content modal-accept">
                    <div class="modal-header modal-header-accept">
                        <h5 class="modal-title" id="acceptPaymentModalLabel_{{ $payment->id }}">
                            <i class="fas fa-check-circle ml-1"></i> قبول المدفوعة
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('supplier.payments.update_status', $payment->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="confirmed">
                        <div class="modal-body">
                            <div class="payment-info">
                                <h6><i class="fas fa-info-circle ml-1"></i> تفاصيل المدفوعة</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>رقم الفاتورة:</strong> {{ $payment->invoice->invoice_number ?? '-' }}</p>
                                        <p><strong>رقم الطلب:</strong> {{ $payment->invoice && $payment->invoice->order ? $payment->invoice->order->order_number : '-' }}</p>
                                        <p><strong>المبلغ المدفوع:</strong> {{ number_format($payment->paid_amount, 2) }} ر.س</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>طريقة الدفع:</strong> {{ $payment->payment_method ?? '-' }}</p>
                                        <p><strong>تاريخ الدفع:</strong> {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('Y/m/d') : '-' }}</p>
                                        <p><strong>حالة الدفع:</strong> <span class="badge badge-warning">معلقة</span></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="notes_{{ $payment->id }}" class="required-field">ملاحظاتك على المدفوعة</label>
                                <textarea class="form-control" id="notes_{{ $payment->id }}" name="notes" rows="4" 
                                    placeholder="أضف ملاحظاتك حول هذه المدفوعة (اختياري)..." maxlength="500"></textarea>
                                <small class="form-text text-muted">يمكنك إضافة ملاحظات توضيحية حول قبول هذه المدفوعة</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times ml-1"></i> إلغاء
                            </button>
                            <button type="submit" class="btn btn-accept">
                                <i class="fas fa-check-double ml-1"></i> قبول المدفوعة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Reject Payment Modals -->
    @foreach($payments_pending as $payment)
        <div class="modal fade" id="rejectPaymentModal_{{ $payment->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectPaymentModalLabel_{{ $payment->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content modal-reject">
                    <div class="modal-header modal-header-reject">
                        <h5 class="modal-title" id="rejectPaymentModalLabel_{{ $payment->id }}">
                            <i class="fas fa-times-circle ml-1"></i> رفض المدفوعة
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('supplier.payments.update_status', $payment->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="rejected">
                        <div class="modal-body">
                            <div class="payment-info">
                                <h6><i class="fas fa-info-circle ml-1"></i> تفاصيل المدفوعة</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>رقم الفاتورة:</strong> {{ $payment->invoice->invoice_number ?? '-' }}</p>
                                        <p><strong>رقم الطلب:</strong> {{ $payment->invoice && $payment->invoice->order ? $payment->invoice->order->order_number : '-' }}</p>
                                        <p><strong>المبلغ المدفوع:</strong> {{ number_format($payment->paid_amount, 2) }} ر.س</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>طريقة الدفع:</strong> {{ $payment->payment_method ?? '-' }}</p>
                                        <p><strong>تاريخ الدفع:</strong> {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('Y/m/d') : '-' }}</p>
                                        <p><strong>حالة الدفع:</strong> <span class="badge badge-warning">معلقة</span></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="rejection_reason_{{ $payment->id }}" class="required-field">سبب الرفض</label>
                                <textarea class="form-control" id="rejection_reason_{{ $payment->id }}" name="rejection_reason" rows="4" 
                                    placeholder="يرجى توضيح سبب رفض هذه المدفوعة..." required maxlength="500"></textarea>
                                <small class="form-text text-muted">يجب توضيح سبب الرفض لمساعدة الصيدلية في تصحيح المشكلة</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times ml-1"></i> إلغاء
                            </button>
                            <button type="submit" class="btn btn-reject">
                                <i class="fas fa-times-circle ml-1"></i> رفض المدفوعة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Invoice Modals -->
    @foreach($payments_pending as $payment)
        @if($payment->invoice)
            <div class="modal fade" id="invoiceModal_{{ $payment->id }}" tabindex="-1" role="dialog" aria-labelledby="invoiceModalLabel_{{ $payment->id }}" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-gradient-primary text-white">
                            <h5 class="modal-title" id="invoiceModalLabel_{{ $payment->id }}">
                                <i class="fas fa-file-invoice ml-1"></i> تفاصيل الفاتورة رقم {{ $payment->invoice->invoice_number }}
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center mb-4">
                                @php
                                    $invStatus = strtolower($payment->invoice->status);
                                @endphp
                                @if($invStatus == 'unpaid')
                                    <span class="badge badge-danger badge-lg">
                                        <i class="fas fa-exclamation-circle"></i> غير مدفوعة
                                    </span>
                                @elseif($invStatus == 'paid')
                                    <span class="badge badge-success badge-lg">
                                        <i class="fas fa-check-circle"></i> مدفوعة
                                    </span>
                                @elseif($invStatus == 'partially')
                                    <span class="badge badge-warning badge-lg">
                                        <i class="fas fa-hourglass-half"></i> مدفوعة جزئياً
                                    </span>
                                @endif
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0"><i class="fas fa-info-circle ml-1"></i> معلومات الفاتورة</h6>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>رقم الفاتورة:</strong> {{ $payment->invoice->invoice_number }}</p>
                                            <p><strong>تاريخ الفاتورة:</strong> {{ $payment->invoice->invoice_date ? \Carbon\Carbon::parse($payment->invoice->invoice_date)->format('Y/m/d') : '-' }}</p>
                                            <p><strong>تاريخ الاستحقاق:</strong> {{ $payment->invoice->due_date ? \Carbon\Carbon::parse($payment->invoice->due_date)->format('Y/m/d') : '-' }}</p>
                                            <p><strong>إجمالي الفاتورة:</strong> <span class="badge badge-success">{{ number_format($payment->invoice->total_amount, 2) }} ر.س</span></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-info">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0"><i class="fas fa-shopping-cart ml-1"></i> معلومات الطلب</h6>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>رقم الطلب:</strong> {{ $payment->invoice->order->order_number ?? '-' }}</p>
                                            <p><strong>تاريخ الطلب:</strong> {{ $payment->invoice->order->order_date ? \Carbon\Carbon::parse($payment->invoice->order->order_date)->format('Y/m/d') : '-' }}</p>
                                            <p><strong>حالة الطلب:</strong> 
                                                @if($payment->invoice->order->status == 'pending')
                                                    <span class="badge badge-warning">معلق</span>
                                                @elseif($payment->invoice->order->status == 'confirmed')
                                                    <span class="badge badge-success">مؤكد</span>
                                                @elseif($payment->invoice->order->status == 'rejected')
                                                    <span class="badge badge-danger">مرفوض</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ $payment->invoice->order->status }}</span>
                                                @endif
                                            </p>
                                            <p><strong>ملاحظات:</strong> {{ $payment->invoice->notes ?? 'لا توجد ملاحظات' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-list ml-1"></i> أصناف الفاتورة</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="text-center">#</th>
                                                    <th>اسم الدواء</th>
                                                    <th class="text-center">الكمية</th>
                                                    <th class="text-center">سعر الوحدة</th>
                                                    <th class="text-center">الإجمالي</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($payment->invoice->order && $payment->invoice->order->orderItems)
                                                    @foreach($payment->invoice->order->orderItems as $idx => $item)
                                                        <tr class="product-row">
                                                            <td class="text-center">{{ $idx + 1 }}</td>
                                                            <td>
                                                                <strong>{{ $item->medicine->medicine_name ?? '-' }}</strong>
                                                                @if($item->medicine)
                                                                    <br><small class="text-muted">{{ $item->medicine->description ?? '' }}</small>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge badge-info">{{ $item->quantity }}</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="text-primary">{{ number_format($item->unit_price, 2) }} ر.س</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="item-total text-success font-weight-bold">{{ number_format($item->total_price, 2) }} ر.س</span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted">
                                                            <i class="fas fa-exclamation-triangle ml-1"></i> لا توجد أصناف مرتبطة
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                            @if($payment->invoice->order && $payment->invoice->order->orderItems)
                                                <tfoot class="bg-light">
                                                    <tr>
                                                        <td colspan="4" class="text-left font-weight-bold">الإجمالي الكلي:</td>
                                                        <td class="text-center font-weight-bold text-success">
                                                            {{ number_format($payment->invoice->total_amount, 2) }} ر.س
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times ml-1"></i> إغلاق
                            </button>
                            @if($payment->invoice)
                                <a href="{{ route('invoices.download', $payment->invoice->id) }}" class="btn btn-primary" target="_blank">
                                    <i class="fas fa-download ml-1"></i> تحميل الفاتورة
                                </a>
                                <a href="{{ route('supplier.invoices.show-pdf', $payment->invoice->id) }}" class="btn btn-info" target="_blank">
                                    <i class="fas fa-eye ml-1"></i> عرض الفاتورة
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

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
<!--Internal  Notify js -->
<script src="{{URL::asset('assets/plugins/notify/js/notifIt.js')}}"></script>
<script src="{{URL::asset('assets/plugins/notify/js/notifit-custom.js')}}"></script>

<script>
$(document).ready(function() {
    // تأثيرات بصرية للأزرار
    $('.dropdown-item').hover(
        function() {
            $(this).css('background-color', '#f8f9fa');
        },
        function() {
            $(this).css('background-color', '');
        }
    );
    
    // تأثير النقر على الأزرار
    $('.dropdown-item').click(function() {
        $(this).addClass('active');
        setTimeout(() => {
            $(this).removeClass('active');
        }, 200);
    });

    // التحقق من صحة النماذج
    $('form').on('submit', function(e) {
        var status = $(this).find('input[name="status"]').val();
        
        if (status === 'rejected') {
            var rejectionReason = $(this).find('textarea[name="rejection_reason"]').val().trim();
            if (!rejectionReason) {
                e.preventDefault();
                notif({
                    msg: "يرجى إدخال سبب الرفض",
                    type: "error"
                });
                return false;
            }
        }
        
        // إظهار رسالة تحميل
    
    });

    // عداد الأحرف للنصوص
    $('textarea').on('input', function() {
        var maxLength = $(this).attr('maxlength');
        var currentLength = $(this).val().length;
        var remaining = maxLength - currentLength;
        
        // إزالة عداد الأحرف السابق إن وجد
        $(this).next('.char-counter').remove();
        
        // إضافة عداد الأحرف الجديد
        if (remaining <= 50) {
            $(this).after('<small class="form-text text-danger char-counter">الأحرف المتبقية: ' + remaining + '</small>');
        } else {
            $(this).after('<small class="form-text text-muted char-counter">الأحرف المتبقية: ' + remaining + '</small>');
        }
    });

    // تأثيرات المودال
    $('.modal').on('show.bs.modal', function() {
        $(this).find('.modal-content').css('transform', 'scale(0.7)');
        setTimeout(() => {
            $(this).find('.modal-content').css({
                'transform': 'scale(1)',
                'transition': 'transform 0.3s ease'
            });
        }, 50);
    });

    // تحسين تجربة المستخدم
    $('form').on('submit', function(e) {
    var $btn = $(this).find('.btn-accept, .btn-reject');
    var originalText = $btn.html();
    $btn.html('<i class="fas fa-spinner fa-spin ml-1"></i> جاري المعالجة...');
    $btn.prop('disabled', true);
    // يمكنك إعادة تفعيل الزر بعد فترة إذا أردت
    setTimeout(() => {
        $btn.html(originalText);
        $btn.prop('disabled', false);
    }, 3000);
});
});
</script>
@endsection