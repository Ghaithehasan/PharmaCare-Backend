<!-- main-header opened -->
			<div class="main-header sticky side-header nav nav-item">
				<div class="container-fluid">
					<div class="main-header-left ">
						<div class="responsive-logo">
							<a href="{{ url('/' . $page='index') }}"><img src="{{URL::asset('assets/img/brand/logo.png')}}" class="logo-1" alt="logo"></a>
							<a href="{{ url('/' . $page='index') }}"><img src="{{URL::asset('assets/img/brand/logo-white.png')}}" class="dark-logo-1" alt="logo"></a>
							<a href="{{ url('/' . $page='index') }}"><img src="{{URL::asset('assets/img/brand/favicon.png')}}" class="logo-2" alt="logo"></a>
							<a href="{{ url('/' . $page='index') }}"><img src="{{URL::asset('assets/img/brand/favicon.png')}}" class="dark-logo-2" alt="logo"></a>
						</div>
						<div class="app-sidebar__toggle" data-toggle="sidebar">
							<a class="open-toggle" href="#"><i class="header-icon fe fe-align-left" ></i></a>
							<a class="close-toggle" href="#"><i class="header-icons fe fe-x"></i></a>
						</div>
						<div class="main-header-center mr-3 d-sm-none d-md-none d-lg-block">
							<input class="form-control" placeholder="Search for anything..." type="search"> <button class="btn"><i class="fas fa-search d-none d-md-block"></i></button>
						</div>
					</div>
					<div class="main-header-right">

						<div class="nav nav-item  navbar-nav-right ml-auto">
							<div class="nav-link" id="bs-example-navbar-collapse-1">
								<form class="navbar-form" role="search">
									<div class="input-group">
										<input type="text" class="form-control" placeholder="Search">
										<span class="input-group-btn">
											<button type="reset" class="btn btn-default">
												<i class="fas fa-times"></i>
											</button>
											<button type="submit" class="btn btn-default nav-link resp-btn">
												<svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
											</button>
										</span>
									</div>
								</form>
							</div>

							<div class="dropdown nav-item main-header-notification">
								<a class="new nav-link" href="#">
									<svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
									<span class="pulse"></span>
								</a>
								<div class="dropdown-menu" style="width: 380px; border: none; box-shadow: 0 0 40px rgba(0,0,0,0.1); border-radius: 15px;">
									<div class="menu-header-content" style="background: linear-gradient(45deg, #2c3e50, #3498db); border-radius: 15px 15px 0 0;">
										<div class="d-flex justify-content-between align-items-center p-4">
											<h6 class="dropdown-title mb-0 tx-15 text-white font-weight-semibold" style="font-size: 1.1rem;">الأشعارات</h6>
											@if($supplier->notifications()->where('is_read', false)->count() > 0)
												<form action="{{ route('supplier.notifications.mark-all-as-read') }}" method="POST" class="d-inline">
													@csrf
													<button type="submit" class="btn btn-sm mark-all-btn" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); transition: all 0.3s ease;">تعليم الكل كمقروء</button>
												</form>
											@endif
										</div>
										<p class="dropdown-title-text subtext mb-0 text-white op-6 pb-3 px-4" style="font-size: 0.9rem;">
											@if($supplier->notifications()->where('is_read', false)->count() > 0)
												انت تملك {{ $supplier->notifications()->where('is_read', false)->count() }} اشعارات غير مقروءة
											@else
												لا توجد إشعارات جديدة
											@endif
										</p>
									</div>
									<div class="main-notification-list Notification-scroll" style="max-height: 400px; overflow-y: auto; background: #fff;">
										@php
											$unreadNotifications = $supplier->notifications()
												->where('is_read', false)
												->latest()
												->take(5)
												->get();
											
											$recentReadNotifications = $supplier->notifications()
												->where('is_read', true)
												->latest()
												->take(3)
												->get();
										@endphp

										@if($unreadNotifications->count() > 0)
											@foreach($unreadNotifications as $notification)
												<div class="notification-item" style="transition: all 0.3s ease;">
													<a href="{{ route('supplier.notifications.show', $notification->id) }}" class="text-decoration-none">
													<div class="d-flex align-items-center p-3 notification-content" style="border-bottom: 1px solid rgba(0,0,0,0.05); position: relative; overflow: hidden;">
														<div class="notifyimg" style="width: 45px; height: 45px; border-radius: 12px; background: linear-gradient(45deg, #3498db, #2ecc71); display: flex; align-items: center; justify-content: center; margin-left: 15px; transition: all 0.3s ease;">
															<i class="la la-bell text-white" style="font-size: 1.2rem; transition: transform 0.3s ease;"></i>
														</div>
														<div class="flex-grow-1">
															<h6 class="notification-label mb-1" style="color: #2c3e50; font-size: 0.95rem; font-weight: 600; transition: all 0.3s ease;">{{ $notification->message }}</h6>
															<div class="notification-subtext" style="color: #95a5a6; font-size: 0.85rem; transition: all 0.3s ease;">
																{{ $notification->created_at->diffForHumans() }}
															</div>
														</div>
														<div class="notification-actions">
															<form action="{{ route('supplier.notifications.mark-as-read', $notification->id) }}" method="POST" class="d-inline">
																@csrf
																<button type="submit" class="btn btn-sm mark-read-btn" style="background: #f8f9fa; border: none; color: #3498db; padding: 8px; border-radius: 8px; transition: all 0.3s ease;">
																	تعليم كمقروء <i class="la la-check" style="font-size: 1rem; transition: transform 0.3s ease;"></i>
																</button>
															</form>
														</div>
													</div>
													</a>
												</div>
											@endforeach

											@if($supplier->notifications()->where('is_read', false)->count() > 5)
												<div class="text-center p-2" style="background: #f8f9fa; border-top: 1px solid rgba(0,0,0,0.05);">
													<a href="{{ route('supplier.notifications.index') }}" class="text-primary" style="font-size: 0.9rem; text-decoration: none;">
														عرض المزيد من الإشعارات غير المقروءة
													</a>
												</div>
											@endif
										@endif

										@if($recentReadNotifications->count() > 0)
											@if($unreadNotifications->count() > 0)
												<div class="p-3 text-muted" style="background: #f8f9fa; font-size: 0.9rem; border-bottom: 1px solid rgba(0,0,0,0.05);">
													آخر الإشعارات المقروءة
												</div>
											@endif
											@foreach($recentReadNotifications as $notification)
												<div class="notification-item" style="transition: all 0.3s ease; opacity: 0.7;">
													<a href="{{ route('supplier.notifications.show', $notification->id) }}" class="text-decoration-none">
													<div class="d-flex align-items-center p-3 notification-content" style="border-bottom: 1px solid rgba(0,0,0,0.05); position: relative; overflow: hidden;">
														<div class="notifyimg" style="width: 45px; height: 45px; border-radius: 12px; background: linear-gradient(45deg, #95a5a6, #7f8c8d); display: flex; align-items: center; justify-content: center; margin-left: 15px; transition: all 0.3s ease;">
															<i class="la la-check-circle text-white" style="font-size: 1.2rem; transition: transform 0.3s ease;"></i>
														</div>
														<div class="flex-grow-1">
															<h6 class="notification-label mb-1" style="color: #7f8c8d; font-size: 0.95rem; font-weight: 600; transition: all 0.3s ease;">{{ $notification->message }}</h6>
															<div class="notification-subtext" style="color: #95a5a6; font-size: 0.85rem; transition: all 0.3s ease;">
																{{ $notification->created_at->diffForHumans() }}
															</div>
														</div>
													</div>
													</a>
												</div>
											@endforeach
										@endif

										@if($unreadNotifications->count() == 0 && $recentReadNotifications->count() == 0)
											<div class="text-center p-5" style="background: #f8f9fa;">
												<div style="width: 60px; height: 60px; background: #e9ecef; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; transition: all 0.3s ease;">
													<i class="la la-bell-slash text-muted" style="font-size: 1.8rem;"></i>
												</div>
												<p class="text-muted mb-0" style="font-size: 0.95rem;">لا توجد إشعارات</p>
											</div>
										@endif
									</div>
									<div class="dropdown-footer text-center p-3" style="background: #f8f9fa; border-radius: 0 0 15px 15px;">
										<a href="{{ route('supplier.notifications.index') }}" class="btn btn-block view-all-btn" style="background: linear-gradient(45deg, #2c3e50, #3498db); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500; transition: all 0.3s ease;">
											عرض جميع الأشعارات
										</a>
									</div>
								</div>
							</div>



							<div class="nav-item full-screen fullscreen-button">
								<a class="new nav-link full-screen-link" href="#"><svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-maximize"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path></svg></a>
							</div>
							<div class="dropdown main-profile-menu nav nav-item nav-link">
								<a class="profile-user d-flex" href=""><img alt="" src="{{URL::asset('assets/img/faces/6.jpg')}}"></a>
								<div class="dropdown-menu">
									<div class="main-header-profile bg-primary p-3">
										<div class="d-flex wd-100p">
											<div class="main-img-user"><img alt="" src="{{URL::asset('assets/img/faces/6.jpg')}}" class=""></div>
											<div class="mr-3 my-auto">
												<h6>{{ $supplier->contact_person_name }}</h6><span>{{ $supplier->email }}</span>
											</div>
										</div>
									</div>
									<a class="dropdown-item" href="{{ route('supplier.profile') }}"><i class="bx bx-user-circle"></i>الملف الشخصي</a>
									<a class="dropdown-item" href=""><i class="bx bx-envelope"></i>الرسائل</a>
									<a class="dropdown-item" href="{{ route('supplier.account.settings') }}"><i class="bx bx-slider-alt"></i>اعدادات الحساب</a>
                                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="bx bx-log-out"></i> تسجيل الخروج</button>
                                    </form>
								</div>
							</div>
							<div class="dropdown main-header-message right-toggle">
								<a class="nav-link pr-0" data-toggle="sidebar-left" data-target=".sidebar-left">
									<svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
<!-- /main-header -->

<style>
.mark-all-btn:hover {
    background: rgba(255,255,255,0.3) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.notification-content:hover {
    background: #f8f9fa;
    transform: translateX(5px);
}

.notification-content:hover .notifyimg {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.2);
}

.notification-content:hover .notification-label {
    color: #3498db !important;
}

.notification-content:hover .notification-subtext {
    color: #7f8c8d !important;
}

.mark-read-btn:hover {
    background: #3498db !important;
    color: white !important;
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.2);
}

.mark-read-btn:hover i {
    transform: rotate(360deg);
}

.view-all-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(52, 152, 219, 0.3);
    background: linear-gradient(45deg, #3498db, #2c3e50) !important;
}

.text-center:hover div {
    transform: scale(1.1);
    background: #3498db !important;
}

.text-center:hover div i {
    color: white !important;
}

/* Notification Toast Styles */
.notification-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 300px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    padding: 15px 20px;
    display: flex;
    align-items: center;
    transform: translateX(120%);
    transition: transform 0.3s ease;
}

.notification-toast.show {
    transform: translateX(0);
}

.notification-toast.success {
    border-right: 4px solid #2ecc71;
}

.notification-toast.error {
    border-right: 4px solid #e74c3c;
}

.notification-toast .icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 15px;
}

.notification-toast.success .icon {
    background: rgba(46, 204, 113, 0.1);
    color: #2ecc71;
}

.notification-toast.error .icon {
    background: rgba(231, 76, 60, 0.1);
    color: #e74c3c;
}

.notification-toast .content {
    flex-grow: 1;
}

.notification-toast .title {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 5px;
}

.notification-toast .message {
    color: #7f8c8d;
    font-size: 0.9rem;
}
</style>

<script>
function showNotification(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `notification-toast ${type}`;
    toast.innerHTML = `
        <div class="icon">
            <i class="la ${type === 'success' ? 'la-check-circle' : 'la-times-circle'}"></i>
        </div>
        <div class="content">
            <div class="title">${type === 'success' ? 'تم بنجاح' : 'خطأ'}</div>
            <div class="message">${message}</div>
        </div>
    `;
    
    document.body.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 100);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function updateUnreadCount(count) {
    // Update the unread count in the notification bell
    const unreadCountElement = document.querySelector('.notification-count');
    if (unreadCountElement) {
        unreadCountElement.textContent = count;
    }
    
    // Update the notification header text
    const notificationHeader = document.querySelector('.dropdown-title-text');
    if (notificationHeader) {
        if (count > 0) {
            notificationHeader.textContent = `انت تملك ${count} اشعارات غير مقروءة`;
        } else {
            notificationHeader.textContent = 'لا توجد إشعارات جديدة';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Handle mark as read forms
    document.querySelectorAll('form[action*="mark-as-read"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message);
                    // Remove the notification item
                    const notificationItem = this.closest('.notification-item');
                    if (notificationItem) {
                        notificationItem.style.opacity = '0';
                        setTimeout(() => notificationItem.remove(), 300);
                    }
                    // Update unread count
                    if (data.unreadCount !== undefined) {
                        updateUnreadCount(data.unreadCount);
                    }
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('حدث خطأ أثناء تحديث الإشعار', 'error');
            });
        });
    });

    // Handle mark all as read form
    const markAllForm = document.querySelector('form[action*="mark-all-as-read"]');
    if (markAllForm) {
        markAllForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message);
                    // Remove all unread notifications
                    document.querySelectorAll('.notification-item').forEach(item => {
                        if (!item.classList.contains('read')) {
                            item.style.opacity = '0';
                            setTimeout(() => item.remove(), 300);
                        }
                    });
                    // Update unread count
                    if (data.unreadCount !== undefined) {
                        updateUnreadCount(data.unreadCount);
                    }
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('حدث خطأ أثناء تحديث الإشعارات', 'error');
            });
        });
    }
});
</script>
