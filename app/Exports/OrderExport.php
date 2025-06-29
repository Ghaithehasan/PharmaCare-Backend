<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Order;

class OrderExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $status;
    protected $supplier_id;

    public function __construct($status = null, $supplier_id = null)
    {
        $this->status = $status;
        $this->supplier_id = $supplier_id;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Order::with(['orderItems.medicine', 'supplier'])
            ->when($this->status, function($query) {
                return $query->where('status', $this->status);
            })
            ->when($this->supplier_id, function($query) {
                return $query->where('supplier_id', $this->supplier_id);
            })
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'رقم الطلبية',
            'تاريخ الطلبية',
            'تاريخ الاستحقاق',
            'اسم المورد',
            'حالة الطلبية',
            'المنتجات',
            'الكميات',
            'ملاحظات'
        ];
    }

    public function map($order): array
    {
        $products = $order->orderItems->map(function($item) {
            return $item->medicine->medicine_name ?? 'غير محدد';
        })->implode(', ');

        $quantities = $order->orderItems->map(function($item) {
            return $item->quantity;
        })->implode(', ');
        
        return [
            $order->order_number,
            $order->order_date->format('Y-m-d'),
            $order->delevery_date ? $order->delevery_date->format('Y-m-d') : 'غير محدد',
            $order->supplier->contact_person_name ?? 'غير محدد',
            $this->getStatusInArabic($order->status),
            $products,
            $quantities,
            $order->note ?? 'لا توجد ملاحظات'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2EFDA']
                ]
            ],
            'A:H' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ]
            ]
        ];
    }

    protected function getStatusInArabic($status)
    {
        return match($status) {
            'pending' => 'معلق',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            default => $status
        };
    }
}
