<?php
namespace App\Services;

use App\Models\Hotel;
use App\Models\Order;
use App\Repositories\OrderRepository\OrderRepositoryInterface;

class OrderService
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepo;
    public function __construct(OrderRepositoryInterface $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }

    /**
     * Kiểm tra user có role cụ thể không
     */
    private function hasRole($user, $role)
    {
        return $user->roles()->where('roles_name', $role)->exists();
    }

    /**
     * Kiểm tra user có bất kỳ role nào trong danh sách không
     */
    private function hasAnyRole($user, $roles)
    {
        return $user->roles()->whereIn('roles_name', $roles)->exists();
    }

    public function listItemsOrderByHotelManagerOrOrder($requestData)
    {
        $users = auth()->user();
        // Hotel manager: tự động lấy hotel_id từ user
        if (isset($requestData['hotel_id'])) {
            if ($this->hasRole($users, 'hotel_manager')) {
                $hotel = Hotel::query()->where('hotel_id', $users->hotel_id)->first();
                $items = Order::query()
                    ->leftJoin('tbl_order_details', 'tbl_order.order_code', '=', 'tbl_order_details.order_code')
                    ->where('tbl_order_details.hotel_id', $users->hotel_id)
                    ->paginate(5);
                return view('admin.Hotel.ManagerHotel.Order.manager_order')->with(compact('items', 'hotel'));
            }
            // Admin: phải có hotel_id trong request
            if ($requestData['hotel_id']) {
                $hotel = Hotel::query()->where('hotel_id', $requestData['hotel_id'])->first();
                if (! $hotel) {
                    abort(404, 'Không tìm thấy hotel');
                }
                $items = Order::query()
                    ->leftJoin('tbl_order_details', 'tbl_order.order_code', '=', 'tbl_order_details.order_code')
                    ->where('tbl_order_details.hotel_id', $requestData['hotel_id'])
                    ->paginate(5);
                return view('admin.Hotel.ManagerHotel.Order.manager_order')->with(compact('items', 'hotel'));
            } else {
                // Admin không có hotel_id: hiển thị tất cả order
                $items = $this->orderRepo->getAllByPaginate(5);
                return view('admin.Hotel.ManagerHotel.Order.manager_order')->with(compact('items'));
            }
        } else {
            // Admin: hiển thị tất cả order
            return $this->listItemsOrderByAdmin();
        }
    }

    public function search($requestData)
    {
        $key     = $requestData['key_sreach'] ?? null;
        $type    = $requestData['status'] ?? '0';  // loại tìm kiếm tổng hợp
        $payment = $requestData['payment'] ?? '0'; // 0: tất cả, 7: tại chỗ, 8: Momo
        $hotelId = $requestData['hotel_id'] ?? '0';

        $query = Order::query()->with('payment')->leftJoin('tbl_order_details', 'tbl_order.order_code', '=', 'tbl_order_details.order_code');

        // Tìm kiếm theo mã đơn
        if ($key) {
            $query->where('order_code', 'like', "%{$key}%");
        }
        if ($hotelId && $hotelId != '0') {
            $query->where('hotel_id', (int) $hotelId);
        }

        switch ($type) {
            case '0': // Tất cả → không thêm điều kiện đơn hàng
                break;
            case '1': // Chờ xử lý
                $query->where('order_status', 0);
                break;
            case '2': // Đã từ chối
                $query->where('order_status', -1);
                break;
            case '3': // Đã hủy
                $query->where('order_status', -2);
                break;
            case '4': // Hoàn thành
                $query->whereIn('order_status', [1, 2]);
                break;
            case '5': // Đã thanh toán → dựa trên payment_status = 1
                $query->whereHas('payment', function ($q) {
                    $q->where('payment_status', 1);
                });
                break;
            case '6': // Chưa thanh toán → payment_status = 0
                $query->whereHas('payment', function ($q) {
                    $q->where('payment_status', 0);
                });
                break;
            default:
                return $this->orderRepo->output_item(collect());
        }
        if ($payment != '0') {
            $methodMap = [
                '7' => 1, // "Thanh toán khi nhận phòng" → payment_method = 1 (tiền mặt)
                '8' => 4, // "Thanh toán Momo" → payment_method = 4 (online)
            ];

            if (isset($methodMap[$payment])) {
                $paymentMethod = $methodMap[$payment];
                $query->whereHas('payment', function ($q) use ($paymentMethod) {
                    $q->where('payment_method', $paymentMethod);
                });
            }
        }
        $query->orderBy('order_id', 'DESC');
        $results = $query->paginate(5);
        return $this->orderRepo->output_item($results);
    }

    public function output_item($items)
    {
        /**
         * order_status : 0 ->  Đang chờ duyệt ,  -1 ->  Đơn Phòng Bị Từ Chối ,  1  -> Hoàn Thành Đơn Phòng ,
         **/
        $output = '';
        foreach ($items as $key => $value_order) {
            $output .= '
          <tr>
          <td>' . $value_order->order_code . '</td>
          <td>' . $value_order->start_day . '</td>
          <td>' . $value_order->end_day . '</td>
          <td>';
            if ($value_order->order_status == 0) {
                $output .= '<span class="text-info"><b>Đang Chờ Duyệt</b></span>';
            } else if ($value_order->order_status == -1) {
                $output .= '<span class="text-danger"><b>Đơn Phòng Bị Từ Chối</b></span>';
            } else if ($value_order->order_status == -2) {
                $output .= '<span class="text-danger"><b>Khách Hàng Hủy Đơn</b></span>';
            } else if ($value_order->order_status == 1 || $value_order->order_status == 2) {
                $output .= '<span class="text-warning"><b>Hoàn Thành Đơn Phòng</b></span>';
            }
            $output .= '
          </td>
          <td>';
            if ($value_order->payment->payment_method == 4) {
                $output .= 'Khi Nhận Phòng';
            } else if ($value_order->payment->payment_method == 1) {
                $output .= 'Thanh Toán Momo';
            }
            $output .= '
          </td>
          <td>';
            if ($value_order->payment->payment_status == 0) {
                $output .= 'Chưa Thanh Toán';
            } else if ($value_order->payment->payment_status == 1) {
                $output .= 'Đã Thanh Toán';
            }
            $output .= '
          </td>
          <td>' . $value_order->created_at . '</td>
          <td>';
            if ($value_order->order_status == 0) {
                $output .= '
          <button style="margin-top:10px" class="btn-sm btn-gradient-success btn-rounded btn-fw btn-order-status" data-order_code="' . $value_order->order_code . '" data-order_status="1">Duyệt Đơn <i class="mdi mdi-calendar-check"></i></button> <br>
          <button style="margin-top:10px" class="btn-sm btn-gradient-danger btn-fw btn-order-status"  data-order_code="' . $value_order->order_code . '" data-order_status="-1" >Từ Chối <i class="mdi mdi-calendar-remove"></i></button> <br>';
            }
            if ($value_order->order_status == -1 || $value_order->order_status == 1 || $value_order->order_status == 2 || $value_order->order_status == -2) {
                $user = Auth::user();
                if ($user->roles()->whereIn('roles_name', ['admin', 'manager'])->exists()) {
                    $output .= '<button type="button" class="btn-sm btn-gradient-danger btn-icon-text btn-delete-item mt-2" data-item_id = "' . $value_order->order_id . '">
                <i class="mdi mdi-delete-forever btn-icon-prepend"></i> Xóa Đơn</button><br>';
                }
            }
            // URL thống nhất cho cả admin và hotel_manager
            $viewOrderUrl = 'admin/hotel/manager/order/view-order?order_id=' . $value_order->order_id;
            $output .= '
          <a href="' . URL($viewOrderUrl) . '"><button style="margin-top:10px" class="btn-sm btn-gradient-info btn-rounded btn-fw">Xem Đơn <i class="mdi mdi-eye"></i></button></a> <br>
          </td>
      </tr>';
        }
        echo $output;
    }

    public function listItemsOrderByAdmin()
    {
        $users = auth()->user();
        // Admin: hiển thị tất cả order
        if ($this->hasRole($users, 'admin')) {
            $items = $this->orderRepo->getAllByPaginate(5);
            return view('admin.Order.manager_order')->with(compact('items'));
        }
    }

    public function getHotels()
    {
        $hotels = Hotel::query()->select('hotel_id', 'hotel_name')->get();
        return $hotels;
    }
}
