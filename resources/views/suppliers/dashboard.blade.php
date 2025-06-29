@extends('layouts.master')
@section('title')
    لوحة التحكم - برنامج الموردين
@stop
@section('css')
    <!--  Owl-carousel css-->
    <link href="{{ URL::asset('assets/plugins/owl-carousel/owl.carousel.css') }}" rel="stylesheet" />
    <!-- Maps css -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.js"></script>

    <link href="{{ URL::asset('assets/plugins/jqvmap/jqvmap.min.css') }}" rel="stylesheet">
	<script src="{{URL::asset('assets/js/chart.flot.js')}}"></script>

    <style>
        /* تصميم البطاقات الأصلية */
        .sales-card {
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .sales-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        
        /* تصميم بطاقات الرسوم البيانية */
        .chart-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            opacity: 0;
            transform: translateY(30px);
            animation: slideInUp 0.8s ease-out forwards;
        }
        
        .chart-card:nth-child(1) { animation-delay: 0.1s; }
        .chart-card:nth-child(2) { animation-delay: 0.2s; }
        .chart-card:nth-child(3) { animation-delay: 0.3s; }
        .chart-card:nth-child(4) { animation-delay: 0.4s; }
        .chart-card:nth-child(5) { animation-delay: 0.5s; }
        .chart-card:nth-child(6) { animation-delay: 0.6s; }
        
        .chart-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }
        
        .chart-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
            background-size: 300% 100%;
            animation: gradientShift 3s ease-in-out infinite;
        }
        
        /* رأس البطاقة */
        .chart-header {
            padding: 20px 25px 15px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .chart-title {
            font-size: 1rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            line-height: 1.2;
        }
        
        .chart-title i {
            font-size: 1.2rem;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            background: rgba(102, 126, 234, 0.1);
            flex-shrink: 0;
        }
        
        /* أسطورة الرسم البياني */
        .chart-legend {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 15px;
            padding: 10px;
            background: rgba(255,255,255,0.8);
            border-radius: 8px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: #495057;
            font-weight: 500;
            padding: 4px 8px;
            border-radius: 4px;
            background: rgba(255,255,255,0.5);
        }
        
        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
            border: 2px solid rgba(255,255,255,0.8);
        }
        
        .legend-color.paid {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        
        .legend-color.unpaid {
            background: linear-gradient(135deg, #dc3545, #fd7e14);
        }
        
        .legend-color.partial {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
        }
        
        /* جسم البطاقة */
        .chart-body {
            padding: 15px 25px 25px;
            position: relative;
            min-height: 280px;
        }
        
        /* تأثيرات الحركة */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        /* تأثيرات التحميل */
        .chart-card.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 40px;
            height: 40px;
            margin: -20px 0 0 -20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* تحسين التخطيط */
        .row-sm {
            margin-bottom: 0;
        }
        
        /* تأثيرات إضافية */
        .chart-card:hover .chart-title i {
            transform: scale(1.1);
            transition: transform 0.3s ease;
        }
        
        /* تحسين الألوان */
        .text-primary { color: #667eea !important; }
        .text-success { color: #28a745 !important; }
        .text-warning { color: #ffc107 !important; }
        .text-danger { color: #dc3545 !important; }
        .text-info { color: #17a2b8 !important; }
        
        /* تحسين المسافات */
        .chart-card + .chart-card {
            margin-top: 0;
        }
        
        /* تأثيرات الظل */
        .chart-card {
            box-shadow: 
                0 10px 30px rgba(0,0,0,0.08),
                0 1px 3px rgba(0,0,0,0.05);
        }
        
        .chart-card:hover {
            box-shadow: 
                0 20px 40px rgba(0,0,0,0.12),
                0 5px 15px rgba(0,0,0,0.08);
        }
        
        /* تحسينات إضافية لمنع تداخل النصوص */
        .chart-card {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        .chart-title {
            word-break: keep-all;
            white-space: nowrap;
        }
        
        .legend-item {
            white-space: nowrap;
            overflow: visible;
            text-overflow: clip;
            min-width: 120px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .chart-legend {
            z-index: 10;
            position: relative;
        }
        
        /* تحسين وضوح النصوص */
        .legend-item span:not(.legend-color) {
            color: #2c3e50 !important;
            font-weight: 600 !important;
            text-shadow: 0 1px 2px rgba(255,255,255,0.8);
        }
        
        /* تحسين المسافات للشاشات الصغيرة */
        @media (max-width: 768px) {
            .chart-header {
                padding: 15px 20px 10px;
            }
            
            .chart-body {
                padding: 10px 20px 20px;
            }
            
            .chart-title {
                font-size: 0.9rem;
            }
            
            .legend-item {
                font-size: 0.8rem;
            }
        }
    </style>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <div>
                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">مرحبا بعودتك مجددا</h2>
                <p class="mg-b-0">نقدم لك لوحة أدارة الموردين - المطور غيث ابراهيم حسن</p>
            </div>
        </div>
        <div class="main-dashboard-header-right">
            <div>
                <label class="tx-13">تقييم العملاء</label>
                <div class="main-star">
                    <i class="typcn typcn-star active"></i> <i class="typcn typcn-star active"></i> <i
                        class="typcn typcn-star active"></i> <i class="typcn typcn-star active"></i> <i
                        class="typcn typcn-star"></i> <span>(14,873)</span>
                </div>
            </div>

        </div>
    </div>
    <!-- /breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row row-sm">
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-primary-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-12 text-white">الطلبات المعلقة</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">
                                   {{ $pendingOrders }}
                                </h4>
                                <p class="mb-0 tx-12 text-white op-7"> عدد الطلبات المعلقة </p>
                            </div>
                            <span class="float-right my-auto mr-auto">
                                <i class="fas fa-clock text-white"></i>
                                <span class="text-white op-7">
                                    {{ $pendingPercentage }}%
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-danger-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-12 text-white">الطلبات الملغية</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h3 class="tx-20 font-weight-bold mb-1 text-white">
                                   {{ $cancelledOrders }}
                                </h3>
                                <p class="mb-0 tx-12 text-white op-7"> عدد الطلبات الملغية
                                </p>
                            </div>
                            <span class="float-right my-auto mr-auto">
                                <i class="fas fa-arrow-circle-down text-white"></i>
                                <span class="text-white op-7">
                                    {{ $cancelledPercentage }}%
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-success-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-12 text-white">الطلبات المقبولة</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">
                                   {{ $confirmedOrders }}
                                </h4>
                                <p class="mb-0 tx-12 text-white op-7">
                                   عدد الطلبات المقبولة
                                </p>
                            </div>
                            <span class="float-right my-auto mr-auto">
                                <i class="fas fa-arrow-circle-up text-white"></i>
                                <span class="text-white op-7">
                                    {{ $confirmedPercentage }}%
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <div class="card overflow-hidden sales-card bg-warning-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                    <div class="">
                        <h6 class="mb-3 tx-12 text-white">الطلبات المكتملة</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">
                                   {{ $completedOrders }}
                                </h4>
                                <p class="mb-0 tx-12 text-white op-7">
                                    عدد الطلبات المكتملة
                                </p>
                            </div>
                            <span class="float-right my-auto mr-auto">
                                <i class="fas fa-arrow-circle-down text-white"></i>
                                <span class="text-white op-7">
                                    {{ $completedPercentage }}%
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
    
    <!-- الرسوم البيانية الاحترافية -->
    <div class="row row-sm">
        <!-- رسم الفواتير الدائري -->
        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h4 class="chart-title">
                        <i class="fas fa-chart-pie text-primary"></i>
                        حالة الفواتير
                    </h4>
                </div>
                <div class="chart-body">
                    <canvas id="invoicesChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <!-- رسم الطلبات العمودي -->
        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h4 class="chart-title">
                        <i class="fas fa-chart-bar text-success"></i>
                        إحصائيات الطلبات
                    </h4>
                </div>
                <div class="chart-body">
                    <canvas id="ordersChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <!-- رسم المبيعات الخطي -->
        <div class="col-xl-4 col-lg-12 col-md-12">
            <div class="chart-card">
                <div class="chart-header">
                    <h4 class="chart-title">
                        <i class="fas fa-chart-line text-warning"></i>
                        تطور المبيعات
                    </h4>
                </div>
                <div class="chart-body">
                    <canvas id="salesChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- رسوم إضافية -->
    <div class="row row-sm">
        <!-- رسم المنطقة -->
        <div class="col-xl-6 col-lg-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h4 class="chart-title">
                        <i class="fas fa-map-marker-alt text-danger"></i>
                        المبيعات حسب المنطقة
                    </h4>
                </div>
                <div class="chart-body">
                    <canvas id="regionChart" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <!-- رسم النشاط اليومي -->
        <div class="col-xl-6 col-lg-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h4 class="chart-title">
                        <i class="fas fa-clock text-info"></i>
                        نشاط الطلبات اليومي
                    </h4>
                </div>
                <div class="chart-body">
                    <canvas id="activityChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Container closed -->
@endsection
@section('js')

    <!--Internal  Chart.bundle js -->
    <script src="{{ URL::asset('assets/plugins/chart.js/Chart.bundle.min.js') }}"></script>
    <!-- Moment js -->
    <script src="{{ URL::asset('assets/plugins/raphael/raphael.min.js') }}"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.js"></script>

    <!--Internal  Flot js-->
    <script src="{{ URL::asset('assets/plugins/jquery.flot/jquery.flot.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/jquery.flot/jquery.flot.pie.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/jquery.flot/jquery.flot.resize.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/jquery.flot/jquery.flot.categories.js') }}"></script>
    <script src="{{ URL::asset('assets/js/dashboard.sampledata.js') }}"></script>
    <script src="{{ URL::asset('assets/js/chart.flot.sampledata.js') }}"></script>
    <!--Internal Apexchart js-->
    <script src="{{ URL::asset('assets/js/apexcharts.js') }}"></script>
    <!-- Internal Map -->
    <script src="{{ URL::asset('assets/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
    <script src="{{ URL::asset('assets/js/modal-popup.js') }}"></script>
    <!--Internal  index js -->
    <script src="{{ URL::asset('assets/js/index.js') }}"></script>
    <script src="{{ URL::asset('assets/js/jquery.vmap.sampledata.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // انتظار تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            // إضافة تأثير التحميل للبطاقات
            const chartCards = document.querySelectorAll('.chart-card');
            chartCards.forEach(card => {
                card.classList.add('loading');
            });
            
            // إنشاء الرسوم البيانية بعد تأخير قصير
            setTimeout(() => {
                createCharts();
                // إزالة تأثير التحميل
                chartCards.forEach(card => {
                    card.classList.remove('loading');
                });
            }, 800);
        });
        
        function createCharts() {
            // 1. رسم الفواتير الدائري
            const invoicesCtx = document.getElementById('invoicesChart').getContext('2d');
            const invoicesChart = new Chart(invoicesCtx, {
                type: 'doughnut',
                data: {
                    labels: ['مدفوع', 'غير مدفوع', 'مدفوع جزئياً'],
                    datasets: [{
                        data: [65, 20, 15],
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(220, 53, 69, 0.8)',
                            'rgba(255, 193, 7, 0.8)'
                        ],
                        borderColor: [
                            'rgba(40, 167, 69, 1)',
                            'rgba(220, 53, 69, 1)',
                            'rgba(255, 193, 7, 1)'
                        ],
                        borderWidth: 3,
                        hoverBorderWidth: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 20,
                            bottom: 20,
                            left: 20,
                            right: 20
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true,
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                },
                                color: '#495057'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed + '%';
                                }
                            }
                        }
                    },
                    animation: {
                        animateRotate: true,
                        animateScale: true,
                        duration: 2000,
                        easing: 'easeOutQuart'
                    }
                }
            });
            
            // 2. رسم الطلبات العمودي
            const ordersCtx = document.getElementById('ordersChart').getContext('2d');
            const ordersChart = new Chart(ordersCtx, {
                type: 'bar',
                data: {
                    labels: ['معلق', 'مقبول', 'مكتمل', 'ملغي'],
                    datasets: [{
                        label: 'عدد الطلبات',
                        data: [{{ $pendingOrders }}, {{ $confirmedOrders }}, {{ $completedOrders }}, {{ $cancelledOrders }}],
                        backgroundColor: [
                            'rgba(102, 126, 234, 0.8)',
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(255, 193, 7, 0.8)',
                            'rgba(220, 53, 69, 0.8)'
                        ],
                        borderColor: [
                            'rgba(102, 126, 234, 1)',
                            'rgba(40, 167, 69, 1)',
                            'rgba(255, 193, 7, 1)',
                            'rgba(220, 53, 69, 1)'
                        ],
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#6c757d',
                                font: {
                                    size: 12
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6c757d',
                                font: {
                                    size: 12
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeOutQuart'
                    }
                }
            });
            
            // 3. رسم المبيعات الخطي
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
                    datasets: [{
                        label: 'المبيعات',
                        data: [25000, 32000, 28000, 45000, 52000, 48000],
                        borderColor: 'rgba(255, 193, 7, 1)',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgba(255, 193, 7, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        pointHoverBackgroundColor: 'rgba(255, 193, 7, 1)',
                        pointHoverBorderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'المبيعات: ₺' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#6c757d',
                                font: {
                                    size: 12
                                },
                                callback: function(value) {
                                    return '₺' + (value/1000) + 'K';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6c757d',
                                font: {
                                    size: 12
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeOutQuart'
                    }
                }
            });
            
            // 4. رسم المبيعات حسب المنطقة
            const regionCtx = document.getElementById('regionChart').getContext('2d');
            const regionChart = new Chart(regionCtx, {
                type: 'bar',
                data: {
                    labels: ['دمشق', 'حلب', 'حمص', 'حماة', 'اللاذقية'],
                    datasets: [{
                        label: 'المبيعات',
                        data: [45000, 38000, 32000, 28000, 25000],
                        backgroundColor: [
                            'rgba(102, 126, 234, 0.8)',
                            'rgba(118, 75, 162, 0.8)',
                            'rgba(17, 153, 142, 0.8)',
                            'rgba(240, 147, 251, 0.8)',
                            'rgba(79, 172, 254, 0.8)'
                        ],
                        borderColor: [
                            'rgba(102, 126, 234, 1)',
                            'rgba(118, 75, 162, 1)',
                            'rgba(17, 153, 142, 1)',
                            'rgba(240, 147, 251, 1)',
                            'rgba(79, 172, 254, 1)'
                        ],
                        borderWidth: 2,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'المبيعات: ₺' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#6c757d',
                                font: {
                                    size: 12
                                },
                                callback: function(value) {
                                    return '₺' + (value/1000) + 'K';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6c757d',
                                font: {
                                    size: 12
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeOutQuart'
                    }
                }
            });
            
            // 5. رسم النشاط اليومي
            const activityCtx = document.getElementById('activityChart').getContext('2d');
            const activityChart = new Chart(activityCtx, {
                type: 'line',
                data: {
                    labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'],
                    datasets: [{
                        label: 'الطلبات',
                        data: [5, 2, 15, 45, 38, 25],
                        borderColor: 'rgba(23, 162, 184, 1)',
                        backgroundColor: 'rgba(23, 162, 184, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgba(23, 162, 184, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#6c757d',
                                font: {
                                    size: 12
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6c757d',
                                font: {
                                    size: 12
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeOutQuart'
                    }
                }
            });
        }
    </script>

@endsection
