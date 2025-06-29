@extends('layouts.master')
@section('title')
لوحة التحكم - اعدادات الحساب
@stop
@section('css')
<style>
    body { background: linear-gradient(120deg, #f8fafc 0%, #e0e7ff 100%); }
    .account-settings-container {
        max-width: 480px;
        margin: 48px auto 32px auto;
        background: #fff;
        border-radius: 22px;
        box-shadow: 0 8px 32px rgba(80, 112, 255, 0.13);
        padding: 40px 32px 32px 32px;
        position: relative;
    }
    .settings-section {
        margin-bottom: 38px;
    }
    .settings-section:last-child {
        margin-bottom: 0;
    }
    .section-header {
        font-size: 1.25em;
        font-weight: bold;
        color: #5b86e5;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: 0.5px;
    }
    .section-header i {
        font-size: 1.4em;
        color: #36d1dc;
        margin-left: 6px;
    }
    .profile-img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #36d1dc33;
        margin-bottom: 16px;
        display: block;
        margin-left: auto;
        margin-right: auto;
    }
    .form-group label {
        font-weight: 500;
        color: #2c3e50;
        margin-bottom: 8px;
        display: block;
    }
    .form-control {
        border-radius: 12px;
        padding: 12px 16px;
        border: 2px solid #e9ecef;
        margin-bottom: 18px;
        font-size: 1.08em;
    }
    .btn-main {
        background: linear-gradient(90deg, #36d1dc, #5b86e5);
        color: #fff;
        border-radius: 10px;
        padding: 12px 32px;
        font-weight: 600;
        border: none;
        transition: 0.2s;
        width: 100%;
        font-size: 1.1em;
        margin-top: 8px;
    }
    .btn-main:hover {
        background: linear-gradient(90deg, #5b86e5, #36d1dc);
        color: #fff;
    }
    .btn-danger {
        background: #e74c3c;
        color: #fff;
        border-radius: 10px;
        padding: 12px 32px;
        font-weight: 600;
        border: none;
        transition: 0.2s;
        width: 100%;
        font-size: 1.1em;
        margin-top: 8px;
    }
    .btn-danger:hover {
        background: #c0392b;
    }
    .divider {
        border: none;
        height: 1px;
        background: linear-gradient(to right, transparent, #e0e7ff, transparent);
        margin: 32px 0 24px 0;
    }
    /* Alerts */
    .custom-alert {
        position: fixed;
        top: 80px;
        right: 24px;
        z-index: 1050;
        max-width: 380px;
        color: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 15px;
        opacity: 0;
        transform: translateX(110%);
        transition: all 0.5s cubic-bezier(.68,-0.55,.27,1.55);
    }
    .custom-alert.show {
        opacity: 1;
        transform: translateX(0);
    }
    .custom-alert.success {
        background: linear-gradient(90deg, #36d1dc, #5b86e5);
    }
    .custom-alert.error {
        background: linear-gradient(90deg, #ff6f61, #e74c3c);
    }
    .custom-alert i {
        font-size: 1.8em;
    }
    .custom-alert .message {
        font-weight: 500;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css"/>
@endsection
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">إعدادات الحساب</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ إدارة الحساب</span>
        </div>
    </div>
</div>
@endsection
@section('content')
@if (session('success'))
    <div class="custom-alert success show" id="success-alert">
        <i class="la la-check-circle"></i>
        <span class="message">{{ session('success') }}</span>
    </div>
@endif
@if ($errors->any())
    <div class="custom-alert error show" id="error-alert">
        <i class="la la-times-circle"></i>
        <span class="message">
            @foreach ($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        </span>
    </div>
@endif
<div class="account-settings-container">
    <div class="settings-section" id="profile">
        <div class="section-header"><i class="la la-user"></i>معلومات الحساب الشخصية</div>
        <form method="POST" action="{{ route('supplier.update.profile') }}" enctype="multipart/form-data">
            @csrf
            <img src="{{ $supplier->profile_image ?? URL::asset('assets/img/faces/6.jpg') }}" class="profile-img" alt="صورة الحساب">
            <input type="file" name="profile_image" class="form-control" style="max-width:220px; margin:auto;">
            <div class="form-group">
                <label>الاسم الكامل</label>
                <input type="text" name="contact_person_name" value="{{ $supplier->contact_person_name }}" class="form-control">
            </div>
            <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" value="{{ $supplier->email }}" class="form-control">
            </div>
            <div class="form-group">
                <label>رقم الجوال</label>
                <input type="text" name="phone" value="{{ $supplier->phone }}" class="form-control">
            </div>
            <button type="submit" class="btn-main">حفظ التغييرات</button>
        </form>
    </div>
    <hr class="divider">
    <div class="settings-section" id="password">
        <div class="section-header"><i class="la la-lock"></i>تغيير كلمة المرور</div>
        <form method="POST" action="{{ route('supplier.update.profile') }}">
            @csrf
            <div class="form-group">
                <label>كلمة المرور الجديدة</label>
                <input type="password" name="password" class="form-control" placeholder="أدخل كلمة المرور الجديدة">
            </div>
            <button type="submit" class="btn-main">تحديث كلمة المرور</button>
        </form>
    </div>
    <hr class="divider">
    <div class="settings-section" id="notifications">
        <div class="section-header"><i class="la la-bell"></i>إعدادات الإشعارات</div>
        <form method="POST" action="#">
            @csrf
            <div class="form-group">
                <label>تلقي إشعارات البريد الإلكتروني</label>
                <input type="checkbox" name="email_notifications" checked> تفعيل
            </div>
            <div class="form-group">
                <label>تلقي إشعارات الرسائل القصيرة</label>
                <input type="checkbox" name="sms_notifications"> تفعيل
            </div>
            <button type="submit" class="btn-main">حفظ الإعدادات</button>
        </form>
    </div>
    <hr class="divider">
    <div class="settings-section" id="delete">
        <div class="section-header"><i class="la la-trash"></i>إدارة الحساب</div>
        <form method="POST" action="{{ route('supplier.delete.account') }}">
            @method('DELETE')
            @csrf
            <button type="submit" class="btn-danger">حذف الحساب نهائيًا</button>
        </form>
    </div>
</div>
@endsection
@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const successAlert = document.getElementById("success-alert");
        const errorAlert = document.getElementById("error-alert");

        if (successAlert) {
            setTimeout(() => {
                successAlert.classList.remove('show');
            }, 4000); // Hide after 4 seconds
        }

        if (errorAlert) {
             setTimeout(() => {
                errorAlert.classList.remove('show');
            }, 6000); // Hide after 6 seconds
        }
    });
</script>
@endsection
