@extends('layouts.master')
@section('title')
الاشعارات - مركز الاشعارات
@endsection

@section('css')
<style>
    /* Ultra Modern Design with Subtle Background */
    body {
        background: #f8fafc;
        min-height: 100vh;
        position: relative;
    }

    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background:
            radial-gradient(circle at 0% 0%, rgba(99, 102, 241, 0.05) 0%, transparent 50%),
            radial-gradient(circle at 100% 0%, rgba(236, 72, 153, 0.05) 0%, transparent 50%),
            radial-gradient(circle at 100% 100%, rgba(59, 130, 246, 0.05) 0%, transparent 50%),
            radial-gradient(circle at 0% 100%, rgba(16, 185, 129, 0.05) 0%, transparent 50%);
        pointer-events: none;
    }

    .notification-container {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 2rem;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.2);
        position: relative;
    }

    .notification-header {
        text-align: center;
        margin-bottom: 3rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid rgba(102, 126, 234, 0.1);
        position: relative;
    }

    .notification-header::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 2px;
        background: linear-gradient(90deg, #667eea, #764ba2);
    }

    .notification-header h3 {
        color: #2d3748;
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 0.8rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    }

    .notification-header p {
        color: #4a5568;
        font-size: 1.1rem;
        opacity: 0.8;
    }

    .notification-item {
        display: flex;
        align-items: center;
        padding: 1.8rem;
        margin-bottom: 1.5rem;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(102, 126, 234, 0.1);
        position: relative;
        overflow: hidden;
    }

    .notification-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(to bottom, #667eea, #764ba2);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .notification-item:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    }

    .notification-item:hover::before {
        opacity: 1;
    }

    .notification-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 16px;
        margin-left: 1.5rem;
        color: white;
        font-size: 1.8rem;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        transition: transform 0.3s ease;
    }

    .notification-item:hover .notification-icon {
        transform: rotate(10deg) scale(1.1);
    }

    .notification-content {
        flex-grow: 1;
        position: relative;
    }

    .notification-title {
        color: #2d3748;
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        transition: color 0.3s ease;
    }

    .notification-time {
        color: #718096;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        opacity: 0.8;
    }

    .notification-time i {
        margin-left: 0.5rem;
        font-size: 0.9rem;
        color: #667eea;
    }

    .unread {
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.85));
        border-right: 4px solid #667eea;
    }

    .unread .notification-title {
        color: #2d3748;
        font-weight: 700;
    }

    .mark-read-btn {
        padding: 0.8rem 1.5rem;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 0.95rem;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
        position: relative;
        overflow: hidden;
    }

    .mark-read-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
    }

    .mark-read-btn:active {
        transform: translateY(0);
    }

    .mark-read-btn i {
        font-size: 1.1rem;
        transition: transform 0.3s ease;
    }

    .mark-read-btn:hover i {
        transform: scale(1.2);
    }

    .no-notifications {
        text-align: center;
        padding: 4rem 2rem;
        color: #4a5568;
        font-size: 1.2rem;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 16px;
        backdrop-filter: blur(5px);
    }

    .no-notifications i {
        font-size: 4rem;
        color: #667eea;
        margin-bottom: 1.5rem;
        opacity: 0.5;
    }

    .no-notifications p {
        font-size: 1.3rem;
        font-weight: 500;
        color: #2d3748;
    }

    /* Advanced Animations */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .notification-item {
        animation: slideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .notification-header {
        animation: fadeIn 0.8s ease-out;
    }

    /* Notification Counter Badge */
    .notification-count {
        position: absolute;
        top: -10px;
        right: -10px;
        background: #e53e3e;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: bold;
        box-shadow: 0 2px 8px rgba(229, 62, 62, 0.3);
    }

    /* Loading Animation */
    .loading-skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }
        100% {
            background-position: -200% 0;
        }
    }

    /* Add new styles for filters */
    .notification-filters {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        border: 1px solid rgba(102, 126, 234, 0.2);
        background: white;
        color: #4a5568;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-btn:hover, .filter-btn.active {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .filter-btn .count {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.2rem 0.5rem;
        border-radius: 12px;
        font-size: 0.8rem;
    }

    .notification-stats {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding: 1rem;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .stat-item {
        text-align: center;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 600;
        color: #2d3748;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #718096;
    }

    .pagination {
        margin-top: 2rem;
        display: flex;
        justify-content: center;
        gap: 0.5rem;
    }

    .pagination .page-item {
        list-style: none;
    }

    .pagination .page-link {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        border: 1px solid rgba(102, 126, 234, 0.2);
        color: #4a5568;
        transition: all 0.3s ease;
    }

    .pagination .page-link:hover {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .pagination .active .page-link {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    /* Modern Alert Notifications */
    .alert-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 400px;
        width: 100%;
    }

    .alert {
        padding: 1.2rem;
        margin-bottom: 1rem;
        border-radius: 16px;
        display: flex;
        align-items: center;
        gap: 1rem;
        background: white;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        border: none;
        position: relative;
        overflow: hidden;
        transform: translateX(120%);
        animation: slideIn 0.5s forwards;
    }

    .alert::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 6px;
        height: 100%;
    }

    .alert-success {
        background: linear-gradient(135deg, #ffffff, #dcfce7);
        border: 1px solid rgba(34, 197, 94, 0.2);
    }

    .alert-success::before {
        background: linear-gradient(to bottom, #22c55e, #16a34a);
    }

    .alert-error {
        background: linear-gradient(135deg, #ffffff, #fee2e2);
        border: 1px solid rgba(239, 68, 68, 0.2);
    }

    .alert-error::before {
        background: linear-gradient(to bottom, #ef4444, #dc2626);
    }

    .alert-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
    }

    .alert-success .alert-icon {
        background: rgba(34, 197, 94, 0.15);
        color: #16a34a;
    }

    .alert-error .alert-icon {
        background: rgba(239, 68, 68, 0.15);
        color: #dc2626;
    }

    .alert-content {
        flex-grow: 1;
    }

    .alert-title {
        font-weight: 700;
        margin-bottom: 0.2rem;
        font-size: 1.1rem;
    }

    .alert-success .alert-title {
        color: #15803d;
    }

    .alert-error .alert-title {
        color: #b91c1c;
    }

    .alert-message {
        font-size: 0.95rem;
        line-height: 1.4;
    }

    .alert-success .alert-message {
        color: #166534;
    }

    .alert-error .alert-message {
        color: #991b1b;
    }

    .alert-close {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
        font-size: 1.4rem;
        transition: all 0.3s ease;
        opacity: 0.7;
    }

    .alert-success .alert-close {
        color: #15803d;
    }

    .alert-error .alert-close {
        color: #b91c1c;
    }

    .alert-close:hover {
        opacity: 1;
        transform: scale(1.1);
    }

    /* Progress Bar */
    .alert-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 4px;
        background: rgba(255, 255, 255, 0.3);
        width: 100%;
    }

    .alert-success .alert-progress-bar {
        background: linear-gradient(to right, #22c55e, #16a34a);
    }

    .alert-error .alert-progress-bar {
        background: linear-gradient(to right, #ef4444, #dc2626);
    }

    @keyframes slideIn {
        from {
            transform: translateX(120%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(120%);
            opacity: 0;
        }
    }

    .alert.hiding {
        animation: slideOut 0.5s forwards;
    }

    @keyframes progress {
        from { width: 100%; }
        to { width: 0%; }
    }

    /* Add a subtle pulse animation to draw attention */
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.02); }
        100% { transform: scale(1); }
    }

    .alert {
        animation: slideIn 0.5s forwards, pulse 2s ease-in-out 2;
    }
</style>
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">الإشعارات</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ مركز الإشعارات</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="notification-container">
    <div class="alert-container">
        @if(session('success'))
            <div class="alert alert-success" role="alert">
                <div class="alert-icon">
                    <i class="bx bx-check-circle"></i>
                </div>
                <div class="alert-content">
                    <div class="alert-title">تم بنجاح</div>
                    <div class="alert-message">{{ session('success') }}</div>
                </div>
                <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                    <i class="bx bx-x"></i>
                </button>
                <div class="alert-progress">
                    <div class="alert-progress-bar"></div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error" role="alert">
                <div class="alert-icon">
                    <i class="bx bx-error-circle"></i>
                </div>
                <div class="alert-content">
                    <div class="alert-title">خطأ</div>
                    <div class="alert-message">{{ session('error') }}</div>
                </div>
                <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                    <i class="bx bx-x"></i>
                </button>
                <div class="alert-progress">
                    <div class="alert-progress-bar"></div>
                </div>
            </div>
        @endif
    </div>

    <div class="notification-header">
        <h3>📢 مركز الإشعارات</h3>
        <p>تابع آخر التحديثات والإشعارات المهمة</p>
    </div>

    <div class="notification-stats">
        <div class="stat-item">
            <div class="stat-value">{{ $unreadCount }}</div>
            <div class="stat-label">إشعارات جديدة</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $notificationsByType['order'] }}</div>
            <div class="stat-label">إشعارات الطلبات</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $notificationsByType['payment'] }}</div>
            <div class="stat-label">إشعارات الدفع</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $notificationsByType['system'] }}</div>
            <div class="stat-label">إشعارات النظام</div>
        </div>
    </div>

    <div class="notification-filters">
        <button class="filter-btn active" data-filter="all">
            <i class="bx bx-bell"></i>
            الكل
        </button>
        <button class="filter-btn" data-filter="order">
            <i class="bx bx-package"></i>
            الطلبات
            <span class="count">{{ $notificationsByType['order'] }}</span>
        </button>
        <button class="filter-btn" data-filter="payment">
            <i class="bx bx-money"></i>
            المدفوعات
            <span class="count">{{ $notificationsByType['payment'] }}</span>
        </button>
        <button class="filter-btn" data-filter="system">
            <i class="bx bx-cog"></i>
            النظام
            <span class="count">{{ $notificationsByType['system'] }}</span>
        </button>
    </div>

    @if($notifications->count() > 0)
        @foreach($notifications as $notification)
            <div class="notification-item {{ $notification->is_read ? 'marked-read' : 'unread' }}"
                 id="notification-{{ $notification->id }}"
                 data-type="{{ $notification->notification_type }}">
                <div class="notification-icon">
                    @switch($notification->notification_type)
                        @case('order')
                            <i class="bx bx-package"></i>
                            @break
                        @case('payment')
                            <i class="bx bx-money"></i>
                            @break
                        @case('system')
                            <i class="bx bx-cog"></i>
                            @break
                        @default
                            <i class="bx bx-bell"></i>
                    @endswitch
                    @if(!$notification->is_read)
                        <span class="notification-count">!</span>
                    @endif
                </div>
                <div class="notification-content">
                    <h5 class="notification-title">{{ $notification->message }}</h5>
                    <div class="notification-time">
                        <i class="bx bx-time"></i>
                        {{ $notification->created_at->diffForHumans() }}
                    </div>
                </div>
                @if(!$notification->is_read)
                    <form action="{{ route('supplier.notifications.mark-as-read', $notification->id) }}"
                          method="POST"
                          style="display: inline;"
                          onsubmit="this.querySelector('button').classList.add('loading')">
                        @csrf
                        <button type="submit" class="mark-read-btn">
                            <i class="bx bx-check"></i>
                            تحديد كمقروء
                        </button>
                    </form>
                @endif
            </div>
        @endforeach

        <div class="pagination">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="no-notifications">
            <i class="bx bx-bell-off"></i>
            <p>لا توجد إشعارات جديدة</p>
        </div>
    @endif
</div>
@endsection

@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Existing filter functionality
        const filterButtons = document.querySelectorAll('.filter-btn');
        const notifications = document.querySelectorAll('.notification-item');

        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');

                const filter = button.dataset.filter;

                notifications.forEach(notification => {
                    if (filter === 'all' || notification.dataset.type === filter) {
                        notification.style.display = 'flex';
                    } else {
                        notification.style.display = 'none';
                    }
                });
            });
        });

        // Enhanced alert handling
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            // Auto-hide after 5 seconds
            setTimeout(() => {
                alert.classList.add('hiding');
                setTimeout(() => alert.remove(), 500);
            }, 5000);

            // Close button functionality
            const closeBtn = alert.querySelector('.alert-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    alert.classList.add('hiding');
                    setTimeout(() => alert.remove(), 500);
                });
            }
        });
    });
</script>
@endsection