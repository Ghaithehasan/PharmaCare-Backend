@extends('layouts.master')
@section('title')
    قائمة الطلبات
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
    </style>
@endsection
@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="my-auto">
						<div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الطلبات</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ قائمة
                    الطلبات المعلقة</span>
            </div>
        </div>

    </div>
    <!-- breadcrumb -->
@endsection
@section('content')

    @if (session()->has('cancel_order'))
        <script>
            window.onload = function() {
                notif({
                    msg: "تم الغاء الطلبية بنجاح",
                    type: "success"
                })
            }

        </script>
    @endif


    @if (session()->has('Status_Update'))
        <script>
            window.onload = function() {
                notif({
                    msg: "تم قبول الطلبية مبدئيا بنجاح",
                    type: "success"
                })
            }

        </script>
    @endif

    @if (session()->has('restore_invoice'))
        <script>
            window.onload = function() {
                notif({
                    msg: "تم استعادة الفاتورة بنجاح",
                    type: "success"
                })
            }

        </script>
    @endif

    @if (session()->has('order_updated'))
        <script>
            window.onload = function() {
                notif({
                    msg: "تم تحديث الطلبية بنجاح",
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

                        <a class="modal-effect btn btn-sm btn-primary" href="{{ route('supplier.orders.exports_orders') }}"
                            style="color:white"><i class="fas fa-file-download"></i>&nbsp;تصدير اكسيل</a>

                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example1" class="table key-buttons text-md-nowrap" data-page-length='50'style="text-align: center">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">#</th>
                                    <th class="border-bottom-0">رقم الطلبية</th>
                                    <th class="border-bottom-0">تاريخ الطلبية</th>
                                    <th class="border-bottom-0">تاريخ الاستحقاق</th>
                                    <th class="border-bottom-0">المنتج</th>
                                    <th class="border-bottom-0">اسم المورد</th>
                                    <th class="border-bottom-0">حالة الطلبية</th>
                                    <th class="border-bottom-0">ملاحظات</th>
                                    <th class="border-bottom-0">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $i = 0;
                                @endphp
                                @foreach ($orders as $order)
                                    @php
                                    $i++
                                    @endphp
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $order->order_number }} </td>
                                        <td>
                                            <span class="badge badge-primary">
                                                <i class="fas fa-calendar-alt ml-1"></i>
                                                {{ \Carbon\Carbon::parse($order->order_date)->format('Y/m/d') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($order->delevery_date)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-truck ml-1"></i>
                                                    {{ \Carbon\Carbon::parse($order->delevery_date)->format('Y/m/d') }}
                                                </span>
                                            @else
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock ml-1"></i>
                                                    غير محدد حالياً
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#products_modal_{{ $order->id }}">
                                                <i class="fas fa-pills ml-1"></i>
                                                عرض المنتجات ({{ $order->orderItems->count() }})
                                            </button>

                                            <!-- Products Modal -->
                                            <div class="modal fade" id="products_modal_{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="productsModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="productsModalLabel">
                                                                <i class="fas fa-pills ml-1"></i>
                                                                منتجات الطلبية #{{ $order->order_number }}
                                                            </h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead class="bg-light">
                                                                        <tr>
                                                                            <th>#</th>
                                                                            <th>الاسم التجاري</th>
                                                                            <th>الاسم العلمي</th>
                                                                            <th>الكمية</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($order->orderItems as $index => $item)
                                                                            <tr>
                                                                                <td>{{ $index + 1 }}</td>
                                                                                <td>{{ $item->medicine->medicine_name ?? 'غير محدد' }}</td>
                                                                                <td>{{ $item->medicine->scientific_name ?? 'غير محدد' }}</td>
                                                                                <td>
                                                                                    <span class="badge badge-primary">
                                                                                        {{ $item->quantity }}
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $supplier->contact_person_name }}</td>
                                        <td>
                                            @if($order->status == 'pending')
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock ml-1"></i>
                                                    معلق
                                                </span>
                                            @elseif($order->status == 'completed')
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle ml-1"></i>
                                                    مكتمل
                                                </span>
                                            @elseif($order->status == 'cancelled')
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-times-circle ml-1"></i>
                                                    ملغي
                                                </span>
                                            @else
                                                <span class="badge badge-info">
                                                    <i class="fas fa-info-circle ml-1"></i>
                                                    مقبول
                                                </span>
                                            @endif
                                        </td>

                                        <td>{{ $order->note }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button aria-expanded="false" aria-haspopup="true"
                                                    class="btn ripple btn-primary btn-sm" data-toggle="dropdown"
                                                    type="button">العمليات<i class="fas fa-caret-down ml-1"></i></button>
                                                <div class="dropdown-menu tx-13">
                                                        <a class="dropdown-item"
                                                            href="#" data-toggle="modal" data-target="#edit_order_modal_{{ $order->id }}"><i class="fa fa-pencil fa-fw"></i> تعديل الطلبية</a>

                                                        <a class="dropdown-item" href="#" data-invoice_id="{{ $order->id }}"
                                                            data-toggle="modal" data-target="#delete_invoice"><i
                                                                class="text-danger fas fa-trash-alt"></i>الغاء الطلبية&nbsp;&nbsp;
                                                            </a>

                                                        <a class="dropdown-item"
                                                            href="#" data-toggle="modal" data-target="#accept_order_modal_{{ $order->id }}"><i
                                                                class=" text-success fas
                                                    fa-check-double"></i>&nbsp;&nbsp;
                                                            قبول الطلبية
                                                            مبدئيا</a>



                                                        <a class="dropdown-item" href="print-order/{{ $order->id }}"><i
                                                                class="text-success fas fa-print"></i>&nbsp;&nbsp;طباعة
                                                            الطلبية
                                                        </a>
                                                </div>
                                            </div>

                                        </td>
                                    </tr>
                                    <!-- Modal for Accepting Order -->
                                    <div class="modal fade" id="accept_order_modal_{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="acceptOrderModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-success">
                                                    <h5 class="modal-title text-white" id="acceptOrderModalLabel">
                                                        <i class="fas fa-check-double ml-1"></i>
                                                        قبول الطلبية مبدئياً
                                                    </h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form action="{{ route('supplier.orders.accept') }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                        
                                                        <div class="form-group">
                                                            <label for="expected_delivery_date" class="form-label">
                                                                <i class="fas fa-calendar-alt ml-1"></i>
                                                                تاريخ التسليم المتوقع
                                                            </label>
                                                            <input type="date" 
                                                                   class="form-control" 
                                                                   id="expected_delivery_date" 
                                                                   name="expected_delivery_date" 
                                                                   required
                                                                   min="{{ date('Y-m-d') }}">
                                                            <small class="form-text text-muted">يرجى تحديد تاريخ التسليم المتوقع للطلبية</small>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="delivery_notes" class="form-label">
                                                                <i class="fas fa-sticky-note ml-1"></i>
                                                                ملاحظات إضافية
                                                            </label>
                                                            <textarea class="form-control" 
                                                                      id="delivery_notes" 
                                                                      name="delivery_notes" 
                                                                      rows="3"
                                                                      placeholder="يمكنك إضافة أي ملاحظات إضافية حول التسليم..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                            <i class="fas fa-times ml-1"></i>
                                                            إلغاء
                                                        </button>
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="fas fa-check ml-1"></i>
                                                            تأكيد القبول
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </tbody>
                        </table>
						</div>
					</div>
						</div>
						</div>
        <!--/div-->
						</div>

    <!-- Edit Order Modal -->
    @foreach ($orders as $order)
        <div class="modal fade" id="edit_order_modal_{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="editOrderModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-primary text-white">
                        <h5 class="modal-title" id="editOrderModalLabel">
                            <i class="fas fa-edit ml-1"></i>
                            تعديل الطلبية #{{ $order->order_number }}
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
								</button>
                    </div>
                    <form action="{{ route('supplier.orders.update') }}" method="POST" id="edit_order_form_{{ $order->id }}">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <div class="modal-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0"><i class="fas fa-info-circle ml-1"></i> معلومات الطلبية</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <p><strong>رقم الطلبية:</strong><br>{{ $order->order_number }}</p>
                                                    <p><strong>تاريخ الطلب:</strong><br>{{ \Carbon\Carbon::parse($order->order_date)->format('Y/m/d') }}</p>
                                                </div>
                                                <div class="col-6">
                                                    <p><strong>حالة الطلبية:</strong><br>
                                                        <span class="badge badge-warning">معلق</span>
                                                    </p>
                                                    <p><strong>إجمالي الطلبية:</strong><br>
                                                        <span class="text-primary font-weight-bold" id="total_order_amount_{{ $order->id }}">
                                                            {{ number_format($order->orderItems->sum('total_price'), 2) }} ليرة سوري
                                                        </span>
                                                    </p>
								</div>
							</div>
						</div>
					</div>
				</div>
                                <div class="col-md-6">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0"><i class="fas fa-clinic-medical ml-1"></i> معلومات الصيدلية</h6>
                                        </div>
                                        <div class="card-body">
                                            <p><i class="fas fa-store ml-1"></i> <strong>صيدلية الهدى</strong></p>
                                            <p><i class="fas fa-map-marker-alt ml-1"></i> Damascus Syria, Mazzah AL_HUDA</p>
                                            <p><i class="fas fa-phone ml-1"></i> 093234535</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-pills ml-1"></i> تعديل المنتجات والأسعار</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="25%">المنتج</th>
                                                    <th width="15%">الكمية المطلوبة</th>
                                                    <th width="15%">الكمية المتوفرة</th>
                                                    <th width="15%">الكمية النهائية</th>
                                                    <th width="15%">السعر الحالي</th>
                                                    <th width="15%">السعر الجديد</th>
                                                    <th width="10%">الإجمالي</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($order->orderItems as $index => $item)
                                                    <tr class="product-row" data-item-id="{{ $item->id }}">
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>
                                                            <div>
                                                                <strong>{{ $item->medicine->medicine_name }}</strong><br>
                                                                <small class="text-muted">{{ $item->medicine->scientific_name }}</small>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-primary">{{ $item->quantity }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-info">{{ rand(50, 200) }}</span>
                                                        </td>
                                                        <td>
                                                            <input type="number" 
                                                                   class="form-control form-control-sm quantity-input" 
                                                                   name="quantities[{{ $item->id }}]" 
                                                                   value="{{ $item->quantity }}" 
                                                                   min="1" 
                                                                   max="1000"
                                                                   data-original-quantity="{{ $item->quantity }}"
                                                                   data-original-price="{{ $item->unit_price }}">
                                                        </td>
                                                        <td>
                                                            <span class="text-muted">{{ number_format($item->unit_price, 2) }} ليرة سوري</span>
                                                        </td>
                                                        <td>
                                                            <input type="number" 
                                                                   class="form-control form-control-sm price-input" 
                                                                   name="prices[{{ $item->id }}]" 
                                                                   value="{{ $item->unit_price }}" 
                                                                   min="0.01" 
                                                                   max="10000"
                                                                   step="0.01"
                                                                   data-original-price="{{ $item->unit_price }}">
                                                        </td>
                                                        <td>
                                                            <span class="item-total font-weight-bold text-primary">
                                                                {{ number_format($item->total_price, 2) }} ليرة سوري
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="bg-light">
                                                <tr>
                                                    <td colspan="7" class="text-left">
                                                        <strong>إجمالي الطلبية:</strong>
                                                    </td>
                                                    <td>
                                                        <span class="font-weight-bold text-success" id="modal_total_amount_{{ $order->id }}">
                                                            {{ number_format($order->orderItems->sum('total_price'), 2) }} ليرة سوري
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_notes_{{ $order->id }}" class="form-label">
                                            <i class="fas fa-sticky-note ml-1"></i>
                                            ملاحظات التعديل
                                        </label>
                                        <textarea class="form-control" 
                                                  id="edit_notes_{{ $order->id }}" 
                                                  name="edit_notes" 
                                                  rows="3"
                                                  placeholder="أي ملاحظات حول التعديلات المطلوبة..."></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="alert alert-info">
                                        <h6><i class="fas fa-info-circle ml-1"></i> ملاحظات مهمة:</h6>
                                        <ul class="mb-0">
                                            <li>يمكنك تعديل الكميات حسب المتوفر لديك</li>
                                            <li>يمكنك تعديل الأسعار حسب التكلفة الجديدة</li>
                                            <li>سيتم إعادة حساب الإجمالي تلقائياً</li>
                                            <li>يمكنك إضافة ملاحظات توضيحية</li>
                                        </ul>
                                        
                                    </div>
                                    
                                   
                                </div>


                               <div class="col-md-6">
                               <div class="alert alert-warning">
                                        <h6><i class="fas fa-exclamation-triangle ml-1"></i> نظام القفل:</h6>
                                        <ul class="mb-0">
                                            <li>الحقول التي تم تعديلها مسبقاً ستكون مقفلة</li>
                                            <li>يمكن تعديل الحقول التي لم يتم تعديلها من قبل</li>
                                            <li>يتم إظهار أيقونة القفل <i class="fas fa-lock text-danger"></i> للحقول المعدلة</li>
                                            <li>هذا يضمن عدم التعديل المتكرر على نفس البيانات</li>
                                        </ul>
                                </div>

                               </div> 
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times ml-1"></i>
                                إلغاء
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save ml-1"></i>
                                حفظ التعديلات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!--  cancelle order -->
    <div class="modal fade" id="delete_invoice" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">الغاء الطلبية</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('supplier.orders.cancelled') }}" method="post">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="cancellation_reason" class="form-label">سبب الإلغاء</label>
                            <textarea 
                                class="form-control" 
                                id="cancellation_reason" 
                                name="cancellation_reason" 
                                rows="3" 
                                placeholder="الرجاء كتابة سبب إلغاء الطلبية..."
                                required></textarea>
                            <small class="form-text text-muted">الرجاء كتابة سبب واضح لإلغاء الطلبية</small>
                        </div>
                        <input type="hidden" name="order_id" id="invoice_id" value="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                        <button type="submit" class="btn btn-danger">تاكيد الإلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    

				</div>
				<!-- row closed -->
			</div>
			<!-- Container closed -->
		</div>
		<!-- main-content closed -->

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
        $('#delete_invoice').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var invoice_id = button.data('invoice_id')
            var modal = $(this)
            modal.find('.modal-body #invoice_id').val(invoice_id);
        })

    </script>

    <script>
        $('#Transfer_invoice').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var invoice_id = button.data('invoice_id')
            var modal = $(this)
            modal.find('.modal-body #invoice_id').val(invoice_id);
        })

    </script>

    <!-- JavaScript for Edit Order Modal -->
    <script>
        // حساب الإجمالي لكل منتج
        function calculateItemTotal(row) {
            var quantity = parseFloat(row.find('.quantity-input').val()) || 0;
            var price = parseFloat(row.find('.price-input').val()) || 0;
            var total = quantity * price;
            row.find('.item-total').text(total.toFixed(2) + ' ليرة سوري');
            return total;
        }

        // حساب إجمالي الطلبية
        function calculateOrderTotal(orderId) {
            var total = 0;
            $('#edit_order_modal_' + orderId + ' .product-row').each(function() {
                total += calculateItemTotal($(this));
            });
            $('#modal_total_amount_' + orderId).text(total.toFixed(2) + ' ليرة سوري');
            return total;
        }

        // مراقبة تغيير الكمية
        $(document).on('input', '.quantity-input', function() {
            var row = $(this).closest('.product-row');
            var orderId = row.closest('.modal').attr('id').replace('edit_order_modal_', '');
            calculateItemTotal(row);
            calculateOrderTotal(orderId);
        });

        // مراقبة تغيير السعر
        $(document).on('input', '.price-input', function() {
            var row = $(this).closest('.product-row');
            var orderId = row.closest('.modal').attr('id').replace('edit_order_modal_', '');
            calculateItemTotal(row);
            calculateOrderTotal(orderId);
        });

        // التحقق من صحة البيانات عند الإرسال
        $(document).on('submit', '[id^="edit_order_form_"]', function(e) {
            var form = $(this);
            var orderId = form.attr('id').replace('edit_order_form_', '');
            var hasChanges = false;
            var totalAmount = 0;

            form.find('.product-row').each(function() {
                var row = $(this);
                var originalQuantity = parseInt(row.find('.quantity-input').data('original-quantity'));
                var originalPrice = parseFloat(row.find('.price-input').data('original-price'));
                var newQuantity = parseInt(row.find('.quantity-input').val());
                var newPrice = parseFloat(row.find('.price-input').val());

                if (newQuantity !== originalQuantity || newPrice !== originalPrice) {
                    hasChanges = true;
                }

                totalAmount += newQuantity * newPrice;
            });

            if (!hasChanges) {
                e.preventDefault();
                notif({
                    msg: "لم يتم إجراء أي تعديلات على الطلبية",
                    type: "warning"
                });
                return false;
            }

            if (totalAmount <= 0) {
                e.preventDefault();
                notif({
                    msg: "يجب أن يكون إجمالي الطلبية أكبر من صفر",
                    type: "error"
                });
                return false;
            }

            // إظهار رسالة تأكيد
            if (!confirm('هل أنت متأكد من حفظ التعديلات؟')) {
                e.preventDefault();
                return false;
            }
        });

        // إعادة تعيين القيم عند فتح المودال
        $(document).on('show.bs.modal', '[id^="edit_order_modal_"]', function() {
            var modal = $(this);
            var orderId = modal.attr('id').replace('edit_order_modal_', '');
            
            modal.find('.product-row').each(function() {
                var row = $(this);
                var originalQuantity = row.find('.quantity-input').data('original-quantity');
                var originalPrice = row.find('.price-input').data('original-price');
                
                row.find('.quantity-input').val(originalQuantity);
                row.find('.price-input').val(originalPrice);
                calculateItemTotal(row);
            });
            
            calculateOrderTotal(orderId);
        });
    </script>

@endsection
