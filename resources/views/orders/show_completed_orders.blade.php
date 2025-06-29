@extends('layouts.master')
@section('title', 'الطلبيات المكتملة')
@section('css')
<style>
    body {
        background: linear-gradient(120deg, #e0e7ff 0%, #f8fafc 100%);
    }
    .completed-orders-container {
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 8px 32px rgba(80, 112, 255, 0.13);
        padding: 40px 32px 32px 32px;
        margin: 48px auto 32px auto;
        max-width: 1200px;
        position: relative;
        overflow: hidden;
    }
    .orders-header {
        background: linear-gradient(90deg, #36d1dc 0%, #5b86e5 100%);
        color: #fff;
        border-radius: 18px;
        padding: 32px 24px 20px 24px;
        margin-bottom: 32px;
        text-align: center;
        box-shadow: 0 4px 24px rgba(80, 112, 255, 0.10);
        position: relative;
    }
    .orders-header h2 {
        margin: 0 0 8px 0;
        font-size: 2.3em;
        font-weight: 800;
        letter-spacing: 1px;
        text-shadow: 0 2px 8px rgba(80, 112, 255, 0.10);
    }
    .orders-header p {
        font-size: 1.15em;
        margin: 0;
        opacity: 0.93;
    }
    .orders-header .header-icon {
        font-size: 3.5em;
        margin-bottom: 10px;
        color: #fff;
        filter: drop-shadow(0 2px 8px #36d1dc88);
        display: block;
        animation: popIn 1s cubic-bezier(.68,-0.55,.27,1.55);
    }
    @keyframes popIn {
        0% { transform: scale(0.7); opacity: 0; }
        80% { transform: scale(1.1); opacity: 1; }
        100% { transform: scale(1); }
    }
    .order-summary {
        margin-bottom: 32px;
        display: flex;
        gap: 32px;
        flex-wrap: wrap;
        justify-content: center;
    }
    .summary-card {
        background: linear-gradient(120deg, #f8fafc 60%, #e0e7ff 100%);
        border-radius: 16px;
        padding: 28px 36px;
        min-width: 240px;
        box-shadow: 0 2px 12px rgba(80, 112, 255, 0.07);
        text-align: center;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s;
    }
    .summary-card:hover {
        transform: translateY(-6px) scale(1.03);
        box-shadow: 0 8px 32px rgba(80, 112, 255, 0.13);
    }
    .summary-card h4 {
        margin: 0 0 10px 0;
        color: #5b86e5;
        font-size: 1.25em;
        font-weight: 700;
    }
    .summary-card .value {
        font-size: 2.1em;
        font-weight: bold;
        color: #36d1dc;
        margin-bottom: 6px;
        letter-spacing: 1px;
    }
    .summary-card .icon {
        font-size: 2.2em;
        color: #5b86e5;
        margin-bottom: 8px;
        display: block;
        opacity: 0.85;
    }
    .table-responsive {
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(80, 112, 255, 0.07);
        background: #f8f9fa;
    }
    .orders-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: transparent;
    }
    .orders-table th, .orders-table td {
        padding: 1.1rem 1rem;
        text-align: right;
    }
    .orders-table th {
        background: #e3e9f7;
        color: #5b86e5;
        font-weight: bold;
        font-size: 1.08em;
        border-bottom: 2px solid #dbeafe;
    }
    .orders-table tr {
        transition: background 0.2s;
    }
    .orders-table tr:hover {
        background: #e0f7fa;
        cursor: pointer;
    }
    .badge {
        display: inline-block;
        padding: 0.5em 1.2em;
        border-radius: 10px;
        font-size: 1em;
        font-weight: 700;
        background: linear-gradient(90deg, #36d1dc 0%, #5b86e5 100%);
        color: #fff;
        box-shadow: 0 2px 8px #36d1dc22;
        letter-spacing: 1px;
    }
    .no-orders {
        text-align: center;
        color: #999;
        font-size: 1.2em;
        padding: 40px 0;
    }
    @media (max-width: 900px) {
        .order-summary { flex-direction: column; gap: 18px; }
        .summary-card { min-width: 180px; padding: 18px 12px; }
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css"/>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الطلبات</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ قائمة الطلبات المكتملة</span>
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
<div class="completed-orders-container">
    <div class="orders-header">
        <span class="header-icon"><i class="la la-check-circle"></i></span>
        <h2>الطلبيات المكتملة</h2>
        <p>هنا تجد جميع الطلبيات التي تم تأكيد استلامها بنجاح، مع ملخص شامل وإحصائيات.</p>
    </div>
    <div class="order-summary">
        <div class="summary-card">
            <span class="icon"><i class="la la-list"></i></span>
            <h4>عدد الطلبيات المكتملة</h4>
            <div class="value">{{ $orders->count() }}</div>
        </div>
        <div class="summary-card">
            <span class="icon"><i class="la la-money-bill-wave"></i></span>
            <h4>إجمالي قيمة الطلبيات</h4>
            <div class="value">
                {{ number_format($orders->sum(function($order) {
                    return $order->orderItems->sum('total_price');
                }), 2) }} ل.س
            </div>
        </div>
        <div class="summary-card">
            <span class="icon"><i class="la la-user-check"></i></span>
            <h4>أكثر مورد تم التعامل معه</h4>
            <div class="value">
                {{ $orders->groupBy('supplier_id')->sortByDesc(function($group){ return $group->count(); })->first()[0]->supplier->contact_person_name ?? '-' }}
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="orders-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>رقم الطلبية</th>
                    <th>المورد</th>
                    <th>تاريخ التسليم</th>
                    <th>عدد الأصناف</th>
                    <th>القيمة الإجمالية</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->supplier->contact_person_name ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($order->delevery_date)->format('Y/m/d') }}</td>
                        <td>{{ $order->orderItems->count() }}</td>
                        <td>{{ number_format($order->orderItems->sum('total_price'), 2) }} ل.س</td>
                        <td><span class="badge">مكتمل</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="no-orders">لا توجد طلبيات مكتملة حالياً.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
