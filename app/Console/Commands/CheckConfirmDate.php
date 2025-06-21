<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Events\OrderLastState;
use Carbon\Carbon;


class CheckConfirmDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-confirm-date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();
        $orders = Order::whereDate('delevery_date', $today)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->get();
    
        foreach ($orders as $order) {
            event(new OrderLastState($order));
        }
    }
}
