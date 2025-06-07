<?php

namespace App\Console\Commands;

use App\Mail\LowStockAlert;
use App\Models\Medicine;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;

class CheckMedicineStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'medicines:check-stock';
    protected $description = 'فحص مخزون الأدوية وإرسال تنبيهات';

    /**
     * The console command description.
     *
     * @var string
     */

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // dd('test');
        $medicines = Medicine::whereRaw('quantity <= alert_quantity')->get();
        
        foreach ($medicines as $medicine) {
            // dd($medicine);
            Mail::to('matrex663@gmail.com')
                ->send(new LowStockAlert($medicine));
        }
    }
}
