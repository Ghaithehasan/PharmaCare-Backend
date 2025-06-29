@extends('layouts.master')
@section('title')
    لوحة التحكم - تعديل الملف الشخصي
@stop
@section('css')
@endsection
@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="my-auto">
						<div class="d-flex">
							<h4 class="content-title mb-0 my-auto">الرأيسية</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ الملف الشخصي</span>
						</div>
					</div>
					{{-- <div class="d-flex my-xl-auto right-content"> --}}


					{{-- </div> --}}
				</div>
				<!-- breadcrumb -->
@endsection
@section('content')
				<!-- row -->
                    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ session()->get('success') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
				<div class="row row-sm">
					<div class="col-lg-4">
						<div class="card mg-b-20">
							<div class="card-body">
								<div class="pl-0">
									<div class="main-profile-overview">
										<div class="main-img-user profile-user">
											<img alt="" src="{{URL::asset('assets/img/faces/6.jpg')}}"><a class="fas fa-camera profile-edit" href="JavaScript:void(0);"></a>
										</div>
										<div class="d-flex justify-content-between mg-b-20">
											<div>
												<h5 class="main-profile-name">{{ $supplier->contact_person_name }}</h5>
												<p class="main-profile-name-text">{{ $supplier->company_name }}</p>
											</div>
										</div>
										<h6>الوصف</h6>
										<div class="main-profile-bio">
                                            نحن موردون متخصصون في تقديم منتجات وخدمات عالية الجودة تلبي احتياجات عملائنا بكفاءة واحترافية. نسعى دائمًا إلى بناء علاقات طويلة الأمد مع شركائنا من خلال التزامنا بالتميز، الموثوقية، وخدمة العملاء المتميزة. نقدم حلولًا مبتكرة في مجال التوريد، ونضمن أفضل الأسعار وأسرع الخدمات لتلبية احتياجات السوق المتغيرة. هدفنا هو أن نكون خيارك الأول للمنتجات والخدمات التي تبحث عنها، مع ضمان أعلى معايير الجودة والشفافية.
											 <a href="">المزيد</a>
										</div><!-- main-profile-bio -->
											<div class="row">
                                            <div class="col-md-4 col mb20">
                                                <h5>{{ $order_completed_count }}</h5>
                                                <h6 class="text-small text-muted mb-0">طلبات مكتملة</h6>
                                            </div>
                                            <div class="col-md-4 col mb20">
                                                <h5>{{ number_format(
												\App\Models\Order::where('supplier_id', $supplier->id)
													->where('status', 'completed')
													->with('orderItems')
													->get()
													->reduce(function($carry, $order) {
														return $carry + $order->orderItems->sum('total_price');
													}, 0)
											) }} ل.س</h5>
                                                <h6 class="text-small text-muted mb-0">إجمالي الإيرادات</h6>
                                            </div>
                                            <div class="col-md-4 col mb20">
                                                <h5>22</h5>
                                                <h6 class="text-small text-muted mb-0">عملاء تم خدمتهم</h6>
                                            </div>
                                        </div>


										<hr class="mg-y-30">

                                        <label class="main-content-label tx-13 mg-b-20">معلومات المورد</label>
                                        <div class="main-profile-social-list">
                                            <div class="media">
                                                <div class="media-icon bg-primary-transparent text-primary">
                                                    <i class="icon ion-md-locate"></i>
                                                </div>
                                                <div class="media-body">
                                                    <span>عنوان المقر الرئيسي</span>
                                                    <p>{{ $supplier->address ?? 'لم يتم تحديد العنوان' }}</p>
                                                </div>
                                            </div>

                                            <div class="media">
                                                <div class="media-icon bg-success-transparent text-success">
                                                    <i class="icon ion-md-call"></i>
                                                </div>
                                                <div class="media-body">
                                                    <span>رقم الدعم الفني</span>
                                                    <p>{{ $supplier->phone }}</p>
                                                </div>
                                            </div>

                                            <div class="media">
                                                <div class="media-icon bg-info-transparent text-info">
                                                    <i class="icon ion-md-mail"></i>
                                                </div>
                                                <div class="media-body">
                                                    <span>البريد الإلكتروني الرسمي</span>
                                                    <p>{{ $supplier->email ?? 'غير متاح' }}</p>
                                                </div>
                                            </div>

                                            <div class="media">
                                                <div class="media-icon bg-danger-transparent text-danger">
                                                    <i class="icon ion-md-globe"></i>
                                                </div>
                                                <div class="media-body">
                                                    <span>موقع الشركة</span>
                                                    <a href="">زيارة الموقع</a>
                                                </div>
                                            </div>
                                        </div>


									</div><!-- main-profile-overview -->
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-8">
						<div class="row row-sm">
							<div class="col-sm-12 col-xl-4 col-lg-12 col-md-12">
								<div class="card ">
									<div class="card-body">
										<div class="counter-status d-flex md-mb-0">
											<div class="counter-icon bg-primary-transparent">
												<i class="icon-layers text-primary"></i>
											</div>
											<div class="mr-auto">
												<h5 class="tx-13">الطلبات المكتملة</h5>
												<h2 class="mb-0 tx-22 mb-1 mt-1">{{ $order_completed_count }}</h2>
												<p class="text-muted mb-0 tx-11"><i class="si si-arrow-up-circle text-success mr-1"></i>زيادة</p>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-12 col-xl-4 col-lg-12 col-md-12">
								<div class="card ">
									<div class="card-body">
										<div class="counter-status d-flex md-mb-0">
											<div class="counter-icon bg-danger-transparent">
												<i class="icon-paypal text-danger"></i>
											</div>
											<div class="mr-auto">
												<h5 class="tx-13">إجمالي الإيرادات</h5>
												<h2 class="mb-0 tx-22 mb-1 mt-1">
													{{ number_format(
														\App\Models\Order::where('supplier_id', $supplier->id)
															->where('status', 'completed')
															->with('orderItems')
															->get()
															->reduce(function($carry, $order) {
																return $carry + $order->orderItems->sum('total_price');
															}, 0)
													) }} ل.س
												</h2>
												<p class="text-muted mb-0 tx-11"><i class="si si-arrow-up-circle text-success mr-1"></i>زيادة</p>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-12 col-xl-4 col-lg-12 col-md-12">
								<div class="card ">
									<div class="card-body">
										<div class="counter-status d-flex md-mb-0">
											<div class="counter-icon bg-success-transparent">
												<i class="icon-rocket text-success"></i>
											</div>
											<div class="mr-auto">
												<h5 class="tx-13">المنتجات المباعة</h5>
												<h2 class="mb-0 tx-22 mb-1 mt-1">
													{{ \App\Models\Order::where('supplier_id', $supplier->id)->where('status', 'completed')->with('orderItems')->get()->reduce(function($carry, $order) {
														return $carry + $order->orderItems->sum('quantity');
													}, 0) }}
												</h2>
												<p class="text-muted mb-0 tx-11"><i class="si si-arrow-up-circle text-success mr-1"></i>زيادة</p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="card">
							<div class="card-body">
								<div class="tabs-menu ">
									<!-- Tabs -->
									<ul class="nav nav-tabs profile navtab-custom panel-tabs">
										<li class="active">
											<a href="#home" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="las la-user-circle tx-16 mr-1"></i></span> <span class="hidden-xs">معلومات شركة التوريد</span> </a>
										</li>
										{{-- <li class="">
											<a href="#profile" data-toggle="tab" aria-expanded="false"> <span class="visible-xs"><i class="las la-images tx-15 mr-1"></i></span> <span class="hidden-xs">GALLERY</span> </a>
										</li> --}}
										<li class="">
											<a href="#settings" data-toggle="tab" aria-expanded="false"> <span class="visible-xs"><i class="las la-cog tx-16 mr-1"></i></span> <span class="hidden-xs">اعدادات ملفك الشخصي</span> </a>
										</li>
									</ul>
								</div>
								<div class="tab-content border-left border-bottom border-right border-top-0 p-4">
									<div class="tab-pane active" id="home">
    <h4 class="tx-15 text-uppercase mb-3">نبذة عن المورد</h4>
    <p class="m-b-5">
         نحن شركة رائدة في توريد المنتجات والخدمات بجودة عالية، نهدف إلى تلبية احتياجات عملائنا بكفاءة واحترافية.
    </p>

    <div class="m-t-30">
        <h4 class="tx-15 text-uppercase mt-3">الخبرة والإنجازات</h4>

        <div class="p-t-10">
            <h5 class="text-primary m-b-5 tx-14">سنوات الخبرة</h5>
            <p><b>10</b></p>
            <p class="text-muted tx-13 m-b-0">نحن نقدم خدمات التوريد منذ 10، ونلتزم بالجودة والموثوقية.</p>
        </div>
        <hr>

        <div class="">
            <h5 class="text-primary m-b-5 tx-14">الشهادات والاعتمادات</h5>
            <p>لا يوجد</p>
            <p class="text-muted tx-13 mb-0">حصلنا على العديد من الاعتمادات لضمان جودة المنتجات والخدمات التي نقدمها.</p>
        </div>
        <hr>

        <div class="">
            <h5 class="text-primary m-b-5 tx-14">مناطق التوزيع</h5>
            <p>نحن نخدم مختلف المناطق المحلية والإقليمية</p>
        </div>
        <hr>

        <div class="">
            <h5 class="text-primary m-b-5 tx-14">السياسات المالية</h5>
            <p>طريقة الدفع: {{ ucfirst($supplier->payment_method) }}</p>
            <p>حد الائتمان: {{ number_format($supplier->credit_limit, 2) }}$</p>
        </div>
    </div>
</div>


									<div class="tab-pane" id="settings">
										<form role="form" method="POST" action="{{ route('supplier.update.profile') }}">
                                            @csrf
											<div class="form-group">
												<label for="FullName">الاسم الكامل</label>
												<input type="text" name="contact_person_name" value="{{ $supplier->contact_person_name }}" id="FullName" class="form-control">
											</div>
											<div class="form-group">
												<label for="Email">البريد الألكتروني</label>
												<input type="email" name="email" value="{{ $supplier->email }}" id="Email" class="form-control">
											</div>
											<div class="form-group">
												<label for="Password">كلمة السر</label>
												<input type="password" name="password" placeholder="6 - 15 حروف" id="Password" class="form-control">
											</div>
											<div class="form-group">
												<label for="RePassword">اعادة كلمة السر</label>
												<input type="password" placeholder="6 - 15 حروف" id="RePassword" class="form-control">
											</div>
											<div class="form-group">
												<label for="AboutMe">الوصف</label>
												<textarea id="AboutMe" name="bio" class="form-control">نحن موردون متخصصون في تقديم منتجات وخدمات عالية الجودة تلبي احتياجات عملائنا بكفاءة واحترافية. نسعى دائمًا إلى بناء علاقات طويلة الأمد مع شركائنا من خلال التزامنا بالتميز، الموثوقية، وخدمة العملاء المتميزة. نقدم حلولًا مبتكرة في مجال التوريد، ونضمن أفضل الأسعار وأسرع الخدمات لتلبية احتياجات السوق المتغيرة. هدفنا هو أن نكون خيارك الأول للمنتجات والخدمات التي تبحث عنها، مع ضمان أعلى معايير الجودة والشفافية</textarea>
											</div>
											<button class="btn btn-primary waves-effect waves-light w-md" type="submit">حفظ</button>
										</form>
									</div>
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
@endsection
