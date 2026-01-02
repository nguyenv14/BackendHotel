<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Http\Enums\OrderStatus;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckNoShowOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:check-no-show';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra và tự động chuyển các đơn hàng quá thời gian check-in sang trạng thái No Show (status = 4)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        
        // Lấy các đơn hàng có:
        // - status = 0 (đã duyệt, chưa check-in)
        // - start_day đã qua (ngày check-in đã qua)
        // - có mã check-in (đã được duyệt)
        $noShowOrders = Order::where('order_status', OrderStatus::WAITING_FOR_APPROVAL)
            ->whereNotNull('checkin_code')
            ->whereDate('start_day', '<', $now->format('Y-m-d'))
            ->get();

        $count = 0;
        foreach ($noShowOrders as $order) {
            // Chuyển status từ 0 sang 4 (No Show)
            $order->order_status = OrderStatus::NO_SHOW;
            $order->save();
            $count++;
            
            $this->info("Đơn hàng {$order->order_code} đã được chuyển sang trạng thái No Show");
        }

        if ($count > 0) {
            $this->info("Đã chuyển {$count} đơn hàng sang trạng thái No Show");
        } else {
            $this->info("Không có đơn hàng nào cần chuyển sang trạng thái No Show");
        }

        return Command::SUCCESS;
    }
}
