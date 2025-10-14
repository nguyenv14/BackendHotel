<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Statistical;
use Carbon\Carbon;

class statistical_run extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistical_run:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d');
        $statical = Statistical::where('order_date', $now)->first();
        if ($statical) {
            $order = Order::where('created_at', 'like', $now . '%')->get();
            $statical->total_order = $order->count();

            $order_completion = Order::where('created_at', 'like', $now . '%')->where('order_status', 1)->get();

            if ($order_completion->count()) {

                $sales = 0;
                $quantity_order_room = 0;
                foreach ($order_completion as $v_order) {

                    $price_room = $v_order->orderdetails->price_room;
                    $hotel_fee = $v_order->orderdetails->hotel_fee;
                    if ($v_order->coupon_name_code != 'Không Có') {
                        $coupon_sale_price = $v_order->coupon_sale_price;
                    } else {
                        $coupon_sale_price = 0;
                    }
                    $sales = $price_room + $hotel_fee - $coupon_sale_price;
                    $count_orderdetails = Order::where('order_code', $v_order->order_code)->count();
                    $quantity_order_room = $count_orderdetails;
                }
                $statical->sales = $sales;
                $statical->quantity_order_room = $quantity_order_room;
            }

            $order_ref = Order::where('created_at', 'like', $now . '%')
                ->where(function ($query) {
                    $query->where('order_status', -1)
                        ->orwhere('order_status', -2);
                })->get();

            if ($order_ref->count()) {
                $price_order_refused = 0;
                $order_refused = $order_ref->count();
                foreach ($order_ref as $v_order) {
                    $price_room = $v_order->orderdetails->price_room;
                    $hotel_fee = $v_order->orderdetails->hotel_fee;
                    if ($v_order->coupon_name_code != 'Không Có') {
                        $coupon_sale_price = $v_order->coupon_sale_price;
                    } else {
                        $coupon_sale_price = 0;
                    }
                    $price_order_refused = $price_room + $hotel_fee - $coupon_sale_price;
                }
                $statical->price_order_refused = $price_order_refused;
                $statical->order_refused = $order_refused;
            }
            $statical->save();
        } else {
            $statis = new Statistical();
            $statis->order_date = $now;
            $statis->sales = 0;
            $statis->order_refused = 0;
            $statis->price_order_refused = 0;
            $statis->quantity_order_room = 0;
            $statis->total_order = 0;
            $statis->save();
        }
        // return Command::SUCCESS;
        // php artisan schedule:work;
    }
}
