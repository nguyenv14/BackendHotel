<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    use SoftDeletes;

    protected $dates = [
        'deleted_at',
    ];
    public $timestamps  = false;
    protected $fillable = [
        'hotel_name', 'hotel_rank', 'hotel_type', 'brand_id', 'area_id', 'hotel_image',
        'hotel_price_average',
        'hotel_placedetails', 'hotel_linkplace', 'hotel_jfameplace',
        'hotel_desc', 'hotel_tag_keyword', 'hotel_view', 'hotel_status', /* Trường Trong Bảng */
    ];
    protected $primaryKey = 'hotel_id'; /* Khóa Chính */
    protected $table      = 'tbl_hotel'; /* Tên Bảng */

    public function getHotelImageAttribute()
    {
        return $this->attributes['hotel_image'] ? asset('public/uploads/hotel/' . $this->attributes['hotel_image']) : null;
    }

    public function brand()
    {
        return $this->belongsTo('App\Models\Brand', 'brand_id');
    }
    public function area()
    {
        return $this->belongsTo('App\Models\Area', 'area_id');
    }
    public function room()
    {
        return $this->belongsTo('App\Models\Room', 'hotel_id', 'hotel_id');
    }
    public function evaluate_details($hotel_id)
    {
        $evaluate                        = Evaluate::where('hotel_id', $hotel_id)->get();
        $count_evaluate                  = $evaluate->count();
        $total_evaluate_loaction_point   = 0;
        $total_evaluate_service_point    = 0;
        $total_evaluate_price_point      = 0;
        $total_evaluate_sanitary_point   = 0;
        $total_evaluate_convenient_point = 0;
        foreach ($evaluate as $key => $v_evaluate) {
            $total_evaluate_loaction_point += $v_evaluate->evaluate_loaction_point;
            $total_evaluate_service_point += $v_evaluate->evaluate_service_point;
            $total_evaluate_price_point += $v_evaluate->evaluate_price_point;
            $total_evaluate_sanitary_point += $v_evaluate->evaluate_sanitary_point;
            $total_evaluate_convenient_point += $v_evaluate->evaluate_convenient_point;
        }
        if ($count_evaluate == 0) {
            $avg_evaluate = 0;
        } else {
            $avg_evaluate_loaction_point   = $total_evaluate_loaction_point / $count_evaluate;
            $avg_evaluate_service_point    = $total_evaluate_service_point / $count_evaluate;
            $avg_evaluate_price_point      = $total_evaluate_price_point / $count_evaluate;
            $avg_evaluate_sanitary_point   = $total_evaluate_sanitary_point / $count_evaluate;
            $avg_evaluate_convenient_point = $total_evaluate_convenient_point / $count_evaluate;

            $avg_evaluate = ($avg_evaluate_loaction_point + $avg_evaluate_service_point + $avg_evaluate_price_point + $avg_evaluate_sanitary_point + $avg_evaluate_convenient_point) / 5;
            $avg_evaluate = number_format($avg_evaluate, 1, '.');
        }
        if ($avg_evaluate == 0) {
            $status = 'Chưa Có Đánh Giá';
        } elseif ($avg_evaluate <= 2) {
            $status = 'Trung Bình';
        } elseif ($avg_evaluate <= 3) {
            $status = 'Tốt';
        } elseif ($avg_evaluate <= 4) {
            $status = 'Tuyệt Vời';
        } elseif ($avg_evaluate <= 5) {
            $status = 'Xuất Sắc';
        }
        $output = '
        <div class="MeliaDanangBeachResort-evaluate-Box-One">
            <i class="fa-solid fa-umbrella"></i>' . $avg_evaluate . '
        </div>
        <div class="MeliaDanangBeachResort-evaluate-Box-Two">
            ' . $status . ' <span style=" color:#4a5568;">(' . $count_evaluate . ' đánh giá)</span>
        </div>
        ';
        return $output;
    }
    public function evaluate($hotel_id)
    {
        $evaluate                        = Evaluate::where('hotel_id', $hotel_id)->get();
        $count_evaluate                  = $evaluate->count();
        $total_evaluate_loaction_point   = 0;
        $total_evaluate_service_point    = 0;
        $total_evaluate_price_point      = 0;
        $total_evaluate_sanitary_point   = 0;
        $total_evaluate_convenient_point = 0;
        foreach ($evaluate as $key => $v_evaluate) {
            $total_evaluate_loaction_point += $v_evaluate->evaluate_loaction_point;
            $total_evaluate_service_point += $v_evaluate->evaluate_service_point;
            $total_evaluate_price_point += $v_evaluate->evaluate_price_point;
            $total_evaluate_sanitary_point += $v_evaluate->evaluate_sanitary_point;
            $total_evaluate_convenient_point += $v_evaluate->evaluate_convenient_point;
        }
        if ($count_evaluate == 0) {
            $avg_evaluate = 0;
        } else {
            $avg_evaluate_loaction_point   = $total_evaluate_loaction_point / $count_evaluate;
            $avg_evaluate_service_point    = $total_evaluate_service_point / $count_evaluate;
            $avg_evaluate_price_point      = $total_evaluate_price_point / $count_evaluate;
            $avg_evaluate_sanitary_point   = $total_evaluate_sanitary_point / $count_evaluate;
            $avg_evaluate_convenient_point = $total_evaluate_convenient_point / $count_evaluate;

            $avg_evaluate = ($avg_evaluate_loaction_point + $avg_evaluate_service_point + $avg_evaluate_price_point + $avg_evaluate_sanitary_point + $avg_evaluate_convenient_point) / 5;
            $avg_evaluate = number_format($avg_evaluate, 1, '.');
        }
        if ($avg_evaluate == 0) {
            $status = 'Chưa Có Đánh Giá';
        } elseif ($avg_evaluate <= 2) {
            $status = 'Trung Bình';
        } elseif ($avg_evaluate <= 3) {
            $status = 'Tốt';
        } elseif ($avg_evaluate <= 4) {
            $status = 'Tuyệt Vời';
        } elseif ($avg_evaluate <= 5) {
            $status = 'Xuất Sắc';
        }
        $output = '
        <div class="trendinghotel_text-evaluate-icon">
            <i class="fa-solid fa-umbrella"></i>' . $avg_evaluate . '
        </div>
        <div class="trendinghotel_text-evaluate-text">
            ' . $status . ' <span style=" color:#4a5568;">(' . $count_evaluate . ' đánh giá)</span>
        </div>
        ';
        return $output;
    }

    public function order_time($hotel_id)
    {
        $output = '';
        Carbon::setLocale('vi');
        $order_details = OrderDetails::where('hotel_id', $hotel_id)->orderby('order_details_id', 'DESC')->first();
        if ($order_details) {
            $create_hotel_order = Carbon::create($order_details->created_at, 'Asia/Ho_Chi_Minh');
            $now                = Carbon::now('Asia/Ho_Chi_Minh');
            $output .= '<div class="trendinghotel_text-time">Vừa đặt cách đây ' . $create_hotel_order->diffForHumans($now) . '</div>';
        } else {
            $output .= '<div class="trendinghotel_text-time-none"></div>';
        }
        return $output;
    }

    public function evaluate_hotel_details($hotel_id)
    {

        $evaluate                        = Evaluate::where('hotel_id', $hotel_id)->get();
        $count_evaluate                  = $evaluate->count();
        $total_evaluate_loaction_point   = 0;
        $total_evaluate_service_point    = 0;
        $total_evaluate_price_point      = 0;
        $total_evaluate_sanitary_point   = 0;
        $total_evaluate_convenient_point = 0;
        foreach ($evaluate as $key => $v_evaluate) {
            $total_evaluate_loaction_point += $v_evaluate->evaluate_loaction_point;
            $total_evaluate_service_point += $v_evaluate->evaluate_service_point;
            $total_evaluate_price_point += $v_evaluate->evaluate_price_point;
            $total_evaluate_sanitary_point += $v_evaluate->evaluate_sanitary_point;
            $total_evaluate_convenient_point += $v_evaluate->evaluate_convenient_point;
        }
        if ($count_evaluate == 0) {
            $avg_evaluate                  = 0;
            $avg_evaluate_loaction_point   = 0;
            $avg_evaluate_service_point    = 0;
            $avg_evaluate_price_point      = 0;
            $avg_evaluate_sanitary_point   = 0;
            $avg_evaluate_convenient_point = 0;
        } else {
            $avg_evaluate_loaction_point   = $total_evaluate_loaction_point / $count_evaluate;
            $avg_evaluate_service_point    = $total_evaluate_service_point / $count_evaluate;
            $avg_evaluate_price_point      = $total_evaluate_price_point / $count_evaluate;
            $avg_evaluate_sanitary_point   = $total_evaluate_sanitary_point / $count_evaluate;
            $avg_evaluate_convenient_point = $total_evaluate_convenient_point / $count_evaluate;

            $avg_evaluate = ($avg_evaluate_loaction_point + $avg_evaluate_service_point + $avg_evaluate_price_point + $avg_evaluate_sanitary_point + $avg_evaluate_convenient_point) / 5;
            $avg_evaluate = number_format($avg_evaluate, 1, '.');
        }
        if ($avg_evaluate == 0) {
            $status = 'Chưa Có Đánh Giá';
        } elseif ($avg_evaluate <= 2) {
            $status = 'Trung Bình';
        } elseif ($avg_evaluate <= 3) {
            $status = 'Tốt';
        } elseif ($avg_evaluate <= 4) {
            $status = 'Tuyệt Vời';
        } elseif ($avg_evaluate <= 5) {
            $status = 'Xuất Sắc';
        }

        $output = '
        <div class="MeliaDanangBeachResort-Bigevaluate_Left">
        <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxleft">
            <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxleft-content">
                <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxleft-content-text">
                    <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxleft-content-text-10">
                        <span>' . $avg_evaluate . '</span>
                    </div>
                    <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxleft-content-text-Good">
                        <span>' . $status . '</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxright">
            <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxright-box">
                <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxright-box-one">
                    <span>Vị trí</span>
                </div>
                <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxright-box-two">

                </div>
                <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxright-box-three">
                    <span>' . number_format($avg_evaluate_loaction_point, 1, '.') . '</span>
                </div>
            </div>

            <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxright-box">
                <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxright-box-one">
                    <span>Phục vụ</span>
                </div>
                <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxright-box-two">

                </div>
                <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxright-box-three">
                    <span>' . number_format($avg_evaluate_service_point, 1, '.') . '</span>
                </div>
            </div>

            <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxright-box">
                <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxright-box-one">
                    <span>Giá cả</span>
                </div>
                <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxright-box-two">

                </div>
                <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxright-box-three">
                    <span>' . number_format($avg_evaluate_price_point, 1, '.') . '</span>
                </div>
            </div>

            <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxright-box">
                <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxright-box-one">
                    <span>Vệ sinh</span>
                </div>
                <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxright-box-two">

                </div>
                <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxright-box-three">
                    <span>' . number_format($total_evaluate_sanitary_point, 1, '.') . '</span>
                </div>
            </div>

            <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxright-box">
                <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxright-box-one">
                    <span>Tiện nghi</span>
                </div>
                <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxright-box-two">

                </div>
                <div class="MeliaDanangBeachResort-Bigevaluate_Left-boxright-box-three">
                    <span>' . number_format($avg_evaluate_convenient_point, 1, '.') . '</span>
                </div>
            </div>
        </div>
    </div>
        ';

        return $output;
    }

}
