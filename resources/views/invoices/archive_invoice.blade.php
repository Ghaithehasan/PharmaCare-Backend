@extends('layouts.master')
@section('title')
    أرشيف الفواتير
@stop
@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
    .archive-header {
        background: #fff;
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        border-left: 4px solid #007bff;
    }
    
    .archive-title {
        font-size: 1.8em;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .archive-subtitle {
        color: #6c757d;
        font-size: 1em;
        margin: 0;
    }
    
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        transition: transform 0.2s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
    }
    
    .stat-icon {
        font-size: 2.5em;
        color: #007bff;
        margin-bottom: 15px;
    }
    
    .stat-number {
        font-size: 2em;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 8px;
    }
    
    .stat-label {
        color: #6c757d;
        font-size: 0.9em;
        font-weight: 500;
    }
    
    .filter-section {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
    }
    
    .filter-title {
        font-size: 1.2em;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .year-filter {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .year-btn {
        padding: 8px 16px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        background: #fff;
        color: #495057;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 0.9em;
    }
    
    .year-btn:hover {
        background: #f8f9fa;
        border-color: #007bff;
        color: #007bff;
        text-decoration: none;
    }
    
    .year-btn.active {
        background: #007bff;
        border-color: #007bff;
        color: #fff;
    }
    
    .invoice-card {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        transition: box-shadow 0.2s ease;
    }
    
    .invoice-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.12);
    }
    
    .invoice-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e9ecef;
    }
    
    .invoice-title {
        font-size: 1.2em;
        font-weight: 600;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .archive-badge {
        background: #28a745;
        color: #fff;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 0.8em;
        font-weight: 500;
    }
    
    .invoice-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .info-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #495057;
        font-size: 0.9em;
    }
    
    .info-item i {
        color: #007bff;
        width: 16px;
    }
    
    .info-item strong {
        color: #2c3e50;
        font-weight: 600;
    }
    
    .invoice-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }
    
    .action-btn {
        padding: 8px 16px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        background: #fff;
        color: #495057;
        text-decoration: none;
        font-size: 0.85em;
        font-weight: 500;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .action-btn:hover {
        background: #f8f9fa;
        border-color: #007bff;
        color: #007bff;
        text-decoration: none;
    }
    
    .btn-view {
        border-color: #17a2b8;
        color: #17a2b8;
    }
    
    .btn-view:hover {
        background: #17a2b8;
        color: #fff;
        border-color: #17a2b8;
    }
    
    .btn-download {
        border-color: #28a745;
        color: #28a745;
    }
    
    .btn-download:hover {
        background: #28a745;
        color: #fff;
        border-color: #28a745;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
    }
    
    .empty-icon {
        font-size: 4em;
        color: #6c757d;
        margin-bottom: 20px;
    }
    
    .empty-title {
        font-size: 1.5em;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    
    .empty-text {
        color: #6c757d;
        margin-bottom: 25px;
    }
    
    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 30px;
    }
    
    @media (max-width: 768px) {
        .invoice-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        
        .invoice-info {
            grid-template-columns: 1fr;
        }
        
        .invoice-actions {
            justify-content: center;
        }
        
        .year-filter {
            justify-content: center;
        }
    }
</style>
@endsection

@section('page-header')
				<div class="breadcrumb-header justify-content-between">
					<div class="my-auto">
						<div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الفواتير</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ أرشيف الفواتير</span>
						</div>
					</div>
				</div>
@endsection

@section('content')
				<div class="row">
    <div class="col-xl-12">
        <!-- Header -->
        <div class="archive-header">
            <h1 class="archive-title">
                <i class="fas fa-archive"></i>
                أرشيف الفواتير
            </h1>
            <p class="archive-subtitle">
                عرض جميع الفواتير المؤرشفة في النظام
            </p>
        </div>

        <!-- Statistics -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-archive"></i>
                </div>
                <div class="stat-number">{{ number_format($archiveStats['total_archived']) }}</div>
                <div class="stat-label">إجمالي الفواتير المؤرشفة</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-number">{{ number_format($archiveStats['this_year']) }}</div>
                <div class="stat-label">فواتير هذا العام</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-number">{{ number_format($archiveStats['total_amount'], 2) }}</div>
                <div class="stat-label">إجمالي المبالغ (ل.س)</div>
            </div>
        </div>

        <!-- Filter -->
        <div class="filter-section">
            <h3 class="filter-title">
                <i class="fas fa-filter"></i>
                تصفية حسب السنة
            </h3>
            <div class="year-filter">
                @foreach($availableYears as $year)
                    <a href="{{ route('supplier.invoices.show_archive') }}?year={{ $year }}" 
                       class="year-btn {{ $selectedYear == $year ? 'active' : '' }}">
                        {{ $year }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Invoices List -->
        @if($invoices->count() > 0)
            @foreach($invoices as $invoice)
                <div class="invoice-card">
                    <div class="invoice-header">
                        <div class="invoice-title">
                            <i class="fas fa-file-invoice-dollar"></i>
                            فاتورة رقم {{ $invoice->invoice_number }}
                        </div>
                        <span class="archive-badge">
                            <i class="fas fa-archive"></i>
                            مؤرشفة
                        </span>
                    </div>
                    
                    <div class="invoice-info">
                        <div class="info-item">
                            <i class="fas fa-hashtag"></i>
                            <strong>رقم الطلبية:</strong> {{ $invoice->order->order_number ?? '-' }}
                        </div>
                        <div class="info-item">
                            <i class="fas fa-user"></i>
                            <strong>المورد:</strong> {{ $invoice->order->supplier->contact_person_name ?? '-' }}
                        </div>
                        <div class="info-item">
                            <i class="fas fa-calendar-alt"></i>
                            <strong>تاريخ الفاتورة:</strong> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('Y/m/d') }}
                        </div>
                        <div class="info-item">
                            <i class="fas fa-calendar-check"></i>
                            <strong>تاريخ الاستحقاق:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('Y/m/d') }}
                        </div>
                        <div class="info-item">
                            <i class="fas fa-money-bill"></i>
                            <strong>الإجمالي:</strong> {{ number_format($invoice->total_amount, 2) }} ليرة سوري
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <strong>تاريخ الأرشفة:</strong> {{ \Carbon\Carbon::parse($invoice->updated_at)->format('Y/m/d H:i') }}
                        </div>
                    </div>
                    
                    <div class="invoice-actions">
                        <a href="{{ route('supplier.invoices.show-pdf', $invoice->id) }}" 
                           class="action-btn btn-view" target="_blank">
                            <i class="fas fa-eye"></i>
                            عرض الفاتورة
                        </a>
                        <a href="{{ route('invoices.download', $invoice->id) }}" 
                           class="action-btn btn-download">
                            <i class="fas fa-download"></i>
                            تحميل الفاتورة
                        </a>
                    </div>
                </div>
            @endforeach
            
            <!-- Pagination -->
            <div class="pagination-container">
                {{ $invoices->appends(['year' => $selectedYear])->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-archive"></i>
                </div>
                <h3 class="empty-title">لا توجد فواتير مؤرشفة</h3>
                <p class="empty-text">
                    لم يتم أرشفة أي فواتير بعد في {{ $selectedYear }}
                </p>
                <a href="{{ route('supplier.invoices.index') }}" class="action-btn btn-view">
                    <i class="fas fa-arrow-left"></i>
                    العودة للفواتير
                </a>
				</div>
        @endif
			</div>
		</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Simple hover effects
        document.querySelectorAll('.stat-card, .invoice-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>
@endsection