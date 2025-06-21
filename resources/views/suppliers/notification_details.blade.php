@extends('layouts.master')
@section('css')
@section('title')
تفاصيل الاشعار
@stop
<style>
    .notification-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }

    .notification-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }

    .notification-header {
        background: linear-gradient(135deg, #6C5CE7 0%, #a8a4e6 100%);
        border-radius: 20px 20px 0 0;
        padding: 2rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .notification-icon {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 1.5rem;
    }

    .notification-icon i {
        font-size: 1.8rem;
        color: #fff;
    }

    .notification-title {
        color: #fff;
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .notification-subtitle {
        color: #fff;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .notification-subtitle .time {
        color: rgba(255, 255, 255, 0.85);
    }

    .notification-subtitle .separator {
        color: rgba(255, 255, 255, 0.5);
    }

    .notification-content {
        padding: 2rem;
    }

    .info-card {
        background: #fff;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 10px rgba(108, 92, 231, 0.05);
        border: 1px solid rgba(108, 92, 231, 0.1);
    }

    .info-card:hover {
        transform: translateX(10px);
        box-shadow: 0 8px 20px rgba(108, 92, 231, 0.1);
    }

    .data-card {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 1.5rem;
        margin-top: 1.5rem;
    }

    .status-badge {
        padding: 0.4rem 1rem;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .status-badge.unread {
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.3);
        animation: pulse 2s infinite;
    }

    .status-badge.read {
        background: rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.8);
    }

    .action-button {
        padding: 0.8rem 1.5rem;
        border-radius: 8px;
        border: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .action-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(108, 92, 231, 0.2);
    }

    .mark-read-btn {
        background: #6C5CE7;
        color: white;
    }

    .mark-read-btn:hover {
        background: #5b4bc4;
    }

    .back-btn {
        background: #f8f9fa;
        color: #6C5CE7;
        border: 1px solid #e9ecef;
    }

    .back-btn:hover {
        background: #e9ecef;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
        }
    }

    .data-item {
        padding: 1rem;
        border-radius: 10px;
        background: #f8f9fa;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
        border: 1px solid rgba(108, 92, 231, 0.05);
    }

    .data-item:hover {
        background: #fff;
        border-color: rgba(108, 92, 231, 0.1);
    }

    .data-label {
        color: #6C5CE7;
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
        font-weight: 600;
    }

    .data-value {
        color: #2d3436;
        font-weight: 500;
    }

    .time-ago {
        color: #636e72;
        font-size: 0.85rem;
        margin-top: 0.3rem;
    }

    .section-title {
        color: #6C5CE7;
        font-weight: 600;
        margin-bottom: 1.5rem;
        position: relative;
        padding-right: 1rem;
    }

    .section-title::after {
        content: '';
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 20px;
        background: #6C5CE7;
        border-radius: 2px;
    }

    .medicines-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-top: 1rem;
    }

    .medicines-table th {
        background: #f8f9fa;
        color: #6C5CE7;
        font-weight: 600;
        padding: 1rem;
        text-align: right;
        border-bottom: 2px solid rgba(108, 92, 231, 0.1);
    }

    .medicines-table td {
        padding: 1rem;
        border-bottom: 1px solid rgba(108, 92, 231, 0.05);
        color: #2d3436;
    }

    .medicines-table tr:hover td {
        background: #f8f9fa;
    }

    .price-value {
        color: #00b894;
        font-weight: 600;
    }

    .quantity-badge {
        background: rgba(108, 92, 231, 0.1);
        color: #6C5CE7;
        padding: 0.3rem 0.8rem;
        border-radius: 6px;
        font-size: 0.85rem;
    }

    .welcome-message {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 1.5rem;
    }

    .features-list {
        margin-top: 1.5rem;
    }

    .feature-item {
        padding: 0.5rem 0;
        color: #2d3436;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .feature-item i {
        color: #00b894;
        font-size: 1.2rem;
    }

    .status-update {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 15px;
        padding: 1.5rem;
    }

    .status-badge {
        display: inline-block;
        padding: 0.3rem 1rem;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .status-badge.old {
        background: #f1f2f6;
        color: #2d3436;
    }

    .status-badge.new {
        background: #6C5CE7;
        color: white;
    }
</style>
@endsection

@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="my-auto">
						<div class="d-flex">
							<h4 class="content-title mb-0 my-auto">الإشعارات</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ تفاصيل الإشعار</span>
						</div>
					</div>
					<!-- <div class="d-flex my-xl-auto right-content">
						<div class="pr-1 mb-3 mb-xl-0">
							<button type="button" class="btn btn-info btn-icon ml-2"><i class="mdi mdi-filter-variant"></i></button>
						</div>
						<div class="pr-1 mb-3 mb-xl-0">
							<button type="button" class="btn btn-danger btn-icon ml-2"><i class="mdi mdi-star"></i></button>
						</div>
						<div class="pr-1 mb-3 mb-xl-0">
							<button type="button" class="btn btn-warning  btn-icon ml-2"><i class="mdi mdi-refresh"></i></button>
						</div>
						<div class="mb-3 mb-xl-0">
							<div class="btn-group dropdown">
								<button type="button" class="btn btn-primary">14 Aug 2019</button>
								<button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" id="dropdownMenuDate" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="sr-only">Toggle Dropdown</span>
								</button>
								<div class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenuDate" data-x-placement="bottom-end">
									<a class="dropdown-item" href="#">2015</a>
									<a class="dropdown-item" href="#">2016</a>
									<a class="dropdown-item" href="#">2017</a>
									<a class="dropdown-item" href="#">2018</a>
								</div>
							</div>
						</div>
					</div> -->
				</div>
				<!-- breadcrumb -->
@endsection
@section('content')
				<!-- row -->
				<div class="row">
					<div class="col-lg-12">
						<div class="notification-card">
							<div class="notification-header">
								<div class="d-flex align-items-center">
									<div class="notification-icon">
										<i class="la la-bell"></i>
									</div>
									<div>
										<h3 class="notification-title">{{ $notification->message }}</h3>
										<div class="notification-subtitle">
											<span class="status-badge {{ $notification->is_read ? 'read' : 'unread' }}">
												{{ $notification->is_read ? 'تم القراءة' : 'جديد' }}
											</span>
											<span class="separator">•</span>
											<span class="time">{{ $notification->created_at->diffForHumans() }}</span>
										</div>
									</div>
								</div>
							</div>

							<div class="notification-content">
								<div class="row">
									<div class="col-md-6">
										<div class="info-card">
											<h5 class="section-title">معلومات الإشعار</h5>
											<div class="data-item">
												<div class="data-label">نوع الإشعار</div>
												<div class="data-value">{{ $notification->notification_type }}</div>
											</div>
											<div class="data-item">
												<div class="data-label">وقت الإرسال</div>
												<div class="data-value">{{ $notification->created_at->format('Y-m-d H:i:s') }}</div>
												<div class="time-ago">{{ $notification->created_at->diffForHumans() }}</div>
											</div>
											<div class="data-item">
												<div class="data-label">آخر تحديث</div>
												<div class="data-value">{{ $notification->updated_at->format('Y-m-d H:i:s') }}</div>
												<div class="time-ago">{{ $notification->updated_at->diffForHumans() }}</div>
											</div>
											@if($notification->read_at)
											<div class="data-item">
												<div class="data-label">وقت القراءة</div>
												<div class="data-value">{{ $notification->read_at->format('Y-m-d H:i:s') }}</div>
												<div class="time-ago">{{ $notification->read_at->diffForHumans() }}</div>
											</div>
											@endif
										</div>
									</div>

									<div class="col-md-6">
										<div class="info-card">
											<h5 class="section-title">بيانات الإشعار</h5>
											@if($notification->data)
												@php
													$data = json_decode($notification->data, true);
												@endphp
												
												@switch($notification->notification_type)
													@case('new_order')
														<div class="data-item">
															<div class="data-label">رقم الطلبية</div>
															<div class="data-value">{{ $data['order_number'] ?? '-' }}</div>
														</div>
														<div class="data-item">
															<div class="data-label">القيمة الإجمالية</div>
															<div class="data-value price-value">{{ number_format($data['total_amount'] ?? 0, 2) }} ل.س</div>
														</div>
														<div class="data-item">
															<div class="data-label">عدد الأدوية</div>
															<div class="data-value">{{ $data['total_medicines'] ?? 0 }} منتج</div>
														</div>
														<div class="data-item">
															<div class="data-label">حالة الطلبية</div>
															<div class="data-value">{{ $data['status'] ?? '-' }}</div>
														</div>

														@if(isset($data['medicines']) && is_array($data['medicines']))
															<h5 class="section-title mt-4">تفاصيل الأدوية</h5>
															<table class="medicines-table">
																<thead>
																	<tr>
																		<th>اسم الدواء</th>
																		<th>الكمية</th>
																		<th>السعر</th>
																		<th>الإجمالي</th>
																	</tr>
																</thead>
																<tbody>
																	@foreach($data['medicines'] as $medicine)
																		<tr>
																			<td>{{ $medicine['name'] }}</td>
																			<td><span class="quantity-badge">{{ $medicine['quantity'] }}</span></td>
																			<td>{{ number_format($medicine['unit_price'], 2) }} ل.س</td>
																			<td class="price-value">{{ number_format($medicine['total_price'], 2) }} ل.س</td>
																		</tr>
																	@endforeach
																</tbody>
															</table>
														@endif
														@break

													@case('welcome')
														<div class="welcome-message">
															<div class="data-item">
																<div class="data-label">رسالة الترحيب</div>
																<div class="data-value">{{ $data['message'] ?? '-' }}</div>
															</div>
															@if(isset($data['features']))
																<div class="features-list mt-3">
																	<h6 class="mb-2">مميزات النظام</h6>
																	<ul class="list-unstyled">
																		@foreach($data['features'] as $feature)
																			<li class="feature-item">
																				<i class="la la-check-circle text-success"></i>
																				{{ $feature }}
																			</li>
																		@endforeach
																	</ul>
																</div>
															@endif
														</div>
														@break

													@case('status_update')
														<div class="data-item">
															<div class="data-label">الحالة السابقة</div>
															<div class="data-value">{{ $data['old_status'] ?? '-' }}</div>
														</div>
														<div class="data-item">
															<div class="data-label">الحالة الجديدة</div>
															<div class="data-value">{{ $data['new_status'] ?? '-' }}</div>
														</div>
														<div class="data-item">
															<div class="data-label">سبب التحديث</div>
															<div class="data-value">{{ $data['reason'] ?? '-' }}</div>
														</div>
														@break

													@default
														<div class="text-muted">نوع إشعار غير معروف</div>
												@endswitch
											@else
												<div class="text-muted">لا توجد بيانات إضافية</div>
											@endif
										</div>
									</div>
								</div>

								<div class="mt-4 d-flex justify-content-between">
									@if(!$notification->is_read)
										<form action="{{ route('supplier.notifications.mark-as-read', $notification->id) }}" method="POST">
											@csrf
											<button type="submit" class="action-button mark-read-btn">
												<i class="la la-check"></i> تعليم كمقروء
											</button>
										</form>
									@endif
									<a href="javascript:history.back()" class="action-button back-btn">
										<i class="la la-arrow-right"></i> العودة للخلف
									</a>
								</div>
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
<script>
    // Add smooth animations when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        const elements = document.querySelectorAll('.notification-card, .info-card, .data-item');
        elements.forEach((element, index) => {
            setTimeout(() => {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
    // window.location.reload();


    Simple form submit handler
    document.querySelector('form[action*="mark-as-read"]').onsubmit = function() {
        console.log('sssssssssssssss');
        console.log('sssssssssssssss');

        setTimeout(function() {
            location.reload();
        }, 100);
    };
</script>
@endsection