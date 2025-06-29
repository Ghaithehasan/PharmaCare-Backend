@extends('layouts.master')
@section('title') المدفوعات المقبولة @stop
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="card mg-b-20">
                <div class="card-header pb-0">
                    <h4 class="mb-0 text-success"><i class="fas fa-check-circle ml-1"></i> قائمة المدفوعات المقبولة</h4>
                </div>
                <div class="card-body">
                    @if($payments_confirmed->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover text-center">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>رقم الفاتورة</th>
                                        <th>رقم الطلب</th>
                                        <th>المبلغ المدفوع</th>
                                        <th>طريقة الدفع</th>
                                        <th>تاريخ الدفع</th>
                                        <th>ملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments_confirmed as $i => $payment)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $payment->invoice->invoice_number ?? '-' }}</td>
                                            <td>{{ $payment->invoice->order->order_number ?? '-' }}</td>
                                            <td><span class="badge badge-success">{{ number_format($payment->paid_amount, 2) }} ر.س</span></td>
                                            <td>{{ $payment->payment_method ?? '-' }}</td>
                                            <td>{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('Y/m/d') : '-' }}</td>
                                            <td>{{ $payment->notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $payments_confirmed->links() }}
                        </div>
                    @else
                        <div class="d-flex flex-column align-items-center justify-content-center py-5" style="min-height: 350px;">
                            <div class="mb-4">
                                <svg width="100" height="100" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="60" cy="60" r="56" fill="#f8f9fa" stroke="#28a745" stroke-width="4"/>
                                    <path d="M40 65L55 80L80 50" stroke="#28a745" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="60" cy="60" r="50" fill="none" stroke="#e9ecef" stroke-width="2"/>
                                </svg>
                            </div>
                            <h4 class="text-success font-weight-bold mb-3">لا توجد مدفوعات مقبولة حالياً</h4>
                            <p class="text-muted">عند قبول أي مدفوعات ستظهر هنا تلقائياً.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

