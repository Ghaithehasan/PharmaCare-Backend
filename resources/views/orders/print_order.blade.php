@extends('layouts.master')
@section('css')
    <style>
        @media print {
            #print_Button {
                display: none;
            }
        }
        .medicine-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border-radius: 8px;
            overflow: hidden;
        }
        .medicine-table th {
            background-color: #f8f9fa;
            padding: 15px;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #e9ecef;
        }
        .medicine-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
            color: #495057;
        }
        .medicine-table tr:hover {
            background-color: #f8f9fa;
        }
        .medicine-name {
            font-weight: 500;
            color: #2c3e50;
        }
        .medicine-scientific {
            font-style: italic;
            color: #6c757d;
        }
        .quantity-badge {
            background-color: #e3f2fd;
            color: #1976d2;
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: 500;
        }
        .order-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .order-title {
            color: #2c3e50;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .order-info {
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .order-date {
            background-color: #e3f2fd;
            color: #1976d2;
            padding: 8px 15px;
            border-radius: 20px;
            display: inline-block;
            font-weight: 500;
            margin: 10px 0;
        }
        .supplier-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        .notes-section {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .print-button {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .print-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.2);
        }
        .pharmacy-info {
            background-color: #e8f5e9;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            border-right: 4px solid #4caf50;
        }
        .supplier-info {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            border-right: 4px solid #2196f3;
        }
        .order-info {
            background-color: #fff3e0;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            border-right: 4px solid #ff9800;
        }
    </style>
@endsection
@section('title')
    معاينة طباعة الطلبية
@stop
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الطلبيات</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ معاينة طباعة الطلبية</span>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="row row-sm">
        <div class="col-md-12 col-xl-12">
            <div class="main-content-body-invoice" id="print">
                <div class="card card-invoice">
                    <div class="card-body">
                        <div class="order-header">
                            <h1 class="order-title">طلبية جديدة</h1>
                            <div class="order-date">
                                <i class="fas fa-calendar-alt ml-2"></i>
                                {{ \Carbon\Carbon::parse($order->order_date)->format('Y/m/d') }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="pharmacy-info">
                                    <h6 class="mb-3">بيانات الصيدلية</h6>
                                    <p class="mb-1"><strong>صيدليتي</strong></p>
                                    <p class="mb-1">دمشق، مزة الهدى</p>
                                    <p class="mb-1">هاتف: 0932589434</p>
                                    <p class="mb-0">البريد الإلكتروني: GhaithHasan@companyname.com</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="supplier-info">
                                    <h6 class="mb-3">بيانات المورد</h6>
                                    <p class="mb-1"><strong>{{ $supplier->contact_person_name }}</strong></p>
                                    <p class="mb-1">{{ $supplier->address }}</p>
                                    <p class="mb-1">هاتف: {{$supplier->phone}}</p>
                                    <p class="mb-0">البريد الإلكتروني: {{$supplier->email}}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="order-info">
                                    <h6 class="mb-3">معلومات الطلبية</h6>
                                    <p class="mb-2">رقم الطلبية: <strong>{{ $order->order_number }}</strong></p>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive mg-t-40">
                            <table class="medicine-table">
                                <thead>
                                    <tr>
                                        <th class="wd-10p">#</th>
                                        <th class="wd-40p">الاسم التجاري</th>
                                        <th class="wd-40p">الاسم العلمي</th>
                                        <th class="tx-center">الكمية المطلوبة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->orderItems as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="medicine-name">{{ $item->medicine->medicine_name ?? 'غير محدد' }}</td>
                                        <td class="medicine-scientific">{{ $item->medicine->scientific_name ?? 'غير محدد' }}</td>
                                        <td class="tx-center">
                                            <span class="quantity-badge">{{ $item->quantity }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="notes-section">
                            <h6 class="mb-2">ملاحظات هامة:</h6>
                            <p class="mb-0">{{$order->note}}</p>
                        </div>

                        <hr class="mg-b-40">
                        <button class="btn print-button float-left mt-3 mr-2" id="print_Button" onclick="printDiv()">
                            <i class="mdi mdi-printer ml-1"></i>طباعة
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        function printDiv() {
            var printContents = document.getElementById('print').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        }
    </script>
@endsection



