<?php
namespace App\Repositories\OrderRepository;

use App\Repositories\BaseRepository;
use App\Models\ManipulationActivity;
use App\Models\Payment;
use App\Models\Orderer;
use App\Models\OrderDetails;
use Auth;
class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    //lấy model tương ứng
    public function getModel()
    {
        return \App\Models\Order::class;
    }

    public function getAllByPaginate($value){
        ManipulationActivity::noteManipulationAdmin( "Xem Danh Sách Đơn Phòng");
        return $this->model->orderby('order_id','DESC')->paginate($value);
    }

    public function searchIDorName($key){
        ManipulationActivity::noteManipulationAdmin( "Tìm Kiếm Danh Sách Đơn Phòng");
        if($key){
            $result = $this->model::where('order_code', 'like', '%' . $key . '%')->get();
            return $result;
        }else{
            $result = $this->model->take(5)->get();
            return $result;
        }
    }

    public function move_bin($id){
        $this->delete($id);
        ManipulationActivity::noteManipulationAdmin( "Chuyển Đơn Phòng( ID : ".$id.") Vào Thùng Rác");
    }

    public function count_bin(){
        $result = $this->model->onlyTrashed()->count();
        return $result;
    }
    public function getItemBinByPaginate($value)
    {
        $result = $this->model->onlyTrashed()->orderby('order_id', 'desc')->paginate($value);
        ManipulationActivity::noteManipulationAdmin( "Xem Đơn Phòng Trong Thùng Rác");
        return $result;
    }
    public function search_bin($key){
        if($key){
            $result = $this->model::onlyTrashed()->where(function ($query) use( $key ) {
                $query->where('order_code', 'like', '%' . $key . '%');
            })->get();
            $output =  $this->output_item_bin($result);
            return $output;
        }else{
            $result =  $this->model::onlyTrashed()->take(5)->get();
            $output = $this->output_item_bin($result);
            return $output;
        }
    }

    public function restore_item($id){
        $result = $this->model->withTrashed()->find($id);
        $result->restore();
        ManipulationActivity::noteManipulationAdmin( "Khôi Phục Đơn Phòng ( ID : ". $id.")");
        return true;
    }

    public function delete_item($id){
        $result = $this->model->withTrashed()->find($id);
        $payment = Payment::where('payment_id',$result['payment_id'])->first();
        $payment->delete();
        $orderer = Orderer::where('orderer_id',$result['orderer_id'])->first();
        $orderer->delete();
        $orderdetails = OrderDetails::where('order_code',$result['order_code'])->get();
        foreach($orderdetails as $order){
            $order->delete();
        }
        $result->forceDelete();
        ManipulationActivity::noteManipulationAdmin( "Xóa Vĩnh Viển Đơn Phòng ( ID : ". $id.")");
        return true;
    }
    
    public function output_item($items){
        /** 
         * order_status : 0 ->  Đang chờ duyệt ,  -1 ->  Đơn Phòng Bị Từ Chối ,  1  -> Hoàn Thành Đơn Phòng ,
        **/
     
        $output = '';
        foreach ($items as $key => $value_order){
          $output .= '
          <tr>
          <td>'. $value_order->order_code.'</td>
          <td>'. $value_order->start_day.'</td>
          <td>'. $value_order->end_day.'</td>
          <td>';
              if($value_order->order_status == 0){
                  $output .= '<span class="text-info"><b>Đang Chờ Duyệt</b></span>';
              }else if($value_order->order_status == -1) {
                  $output .= '<span class="text-danger"><b>Đơn Phòng Bị Từ Chối</b></span>';
              }else if($value_order->order_status == -2) {
                    $output .= '<span class="text-danger"><b>Khách Hàng Hủy Đơn</b></span>';
                }else if($value_order->order_status == 1 || $value_order->order_status == 2) {
                      $output .= '<span class="text-warning"><b>Hoàn Thành Đơn Phòng</b></span>';
              }
          $output .= '
          </td>
          <td>';
          if($value_order->payment->payment_method == 4){
              $output .= 'Khi Nhận Phòng';
          }else if($value_order->payment->payment_method == 1){
              $output .= 'Thanh Toán Momo';
          }
          $output .= '
          </td>
          <td>';
          if($value_order->payment->payment_status == 0){
              $output .= 'Chưa Thanh Toán';
          }else if($value_order->payment->payment_status == 1){
              $output .= 'Đã Thanh Toán';
          }
          $output .= '
          </td>
          <td>'. $value_order->created_at.'</td>
          <td>';
          if($value_order->order_status == 0){
          $output .= '
          <button style="margin-top:10px" class="btn-sm btn-gradient-success btn-rounded btn-fw btn-order-status" data-order_code="'.$value_order->order_code.'" data-order_status="1">Duyệt Đơn <i class="mdi mdi-calendar-check"></i></button> <br>
          <button style="margin-top:10px" class="btn-sm btn-gradient-danger btn-fw btn-order-status"  data-order_code="'.$value_order->order_code.'" data-order_status="-1" >Từ Chối <i class="mdi mdi-calendar-remove"></i></button> <br>';
          }
          if($value_order->order_status == -1 || $value_order->order_status == 1 || $value_order->order_status == 2 || $value_order->order_status == -2){
            if(Auth::user()->hasAnyRoles(['admin','manager'])) {
                $output .= '<button type="button" class="btn-sm btn-gradient-danger btn-icon-text btn-delete-item mt-2" data-item_id = "'. $value_order->order_id.'">
                <i class="mdi mdi-delete-forever btn-icon-prepend"></i> Xóa Đơn</button><br>';
            }
          }
  
          $output .= '
          <a href="'.URL('admin/order/view-order?order_id=' . $value_order->order_id).'"><button style="margin-top:10px" class="btn-sm btn-gradient-info btn-rounded btn-fw">Xem Đơn <i class="mdi mdi-eye"></i></button></a> <br>
          </td>
      </tr>';
        }
        echo $output;
       
      }
    public function output_item_bin($items)
    {
        $output = '';
        foreach ($items as $key => $value_order){
          $output .= '
          <tr>
          <td>'. $value_order->order_code.'</td>
          <td>'. $value_order->start_day.'</td>
          <td>'. $value_order->end_day.'</td>
          <td>';
              if($value_order->order_status == 0){
                  $output .= '<span class="text-info"><b>Đang Chờ Duyệt</b></span>';
              }else if($value_order->order_status == -1) {
                  $output .= '<span class="text-danger"><b>Đơn Phòng Bị Từ Chối</b></span>';
              }else if($value_order->order_status == -2) {
                $output .= '<span class="text-danger"><b>Khách Hàng Hủy Đơn</b></span>';
              }
              else if($value_order->order_status == 1 || $value_order->order_status == 2) {
                  $output .= '<span class="text-warning"><b>Hoàn Thành Đơn Phòng</b></span>';
              }
          $output .= '
          </td>
          <td>';
          if($value_order->payment->payment_method == 4){
              $output .= 'Khi Nhận Phòng';
          }else if($value_order->payment->payment_method == 1){
              $output .= 'Thanh Toán Momo';
          }
          $output .= '
          </td>
          <td>';
          if($value_order->payment->payment_status == 0){
              $output .= 'Chưa Thanh Toán';
          }else if($value_order->payment->payment_status == 1){
              $output .= 'Đã Thanh Toán';
          }
          $output .= '
          </td>
          <td>'.$value_order->deleted_at.'</td> 
          <td>
              <button type="button" class="btn-sm btn-gradient-success btn-icon-text btn-restore-item" data-item_id = "'. $value_order->order_id.'">
              <i class="mdi mdi-backup-restore btn-icon-prepend"></i> Khôi Phục </button>
              </br>
              <button type="button" class="btn-sm btn-gradient-danger btn-icon-text mt-3 btn-delete-item" data-item_id = "'. $value_order->order_id.'">
              <i class="mdi mdi-delete-forever btn-icon-prepend"></i> Xóa </button>
          </td>
      </tr>';
        }
        echo $output;
    }


    public function message($type,$content){
        $message = array(
            "type" => "$type",
            "content" => "$content",
        ); 
        session()->flash('message', $message);
    }


}