<?php
namespace App\Repositories\ServiceChargeRepository;

use App\Repositories\BaseRepository;
use App\Models\ManipulationActivity;
use App\Models\Hotel;
use Auth;
class ServiceChargeRepository extends BaseRepository implements ServiceChargeRepositoryInterface
{
    //lấy model tương ứng
    public function getModel()
    {
        return \App\Models\ServiceCharge::class;
    }

    public function getAllHotel(){
        return Hotel::get();
    }

    public function getAllByPaginate($value){
        ManipulationActivity::noteManipulationAdmin( "Xem Danh Sách Phí Dịch Vụ");
        return $this->model->paginate($value);
    }

    // public function searchIDorName($key){
    //     ManipulationActivity::noteManipulationAdmin( "Tìm Kiếm Danh Sách Phí Dịch Vụ");
    //     if($key){
    //         $result = $this->model::where('servicecharge_id', 'like', '%' . $key . '%')->orwhere('servicecharge_name', 'like', '%' . $key . '%')->get();
    //         return $result;
    //     }else{
    //         $result = $this->model->take(5)->get();
    //         return $result;
    //     }
    // }

    public function insert_item($data){
        unset($data['_token']);
        $servicecharge_id= $this->model->insertGetId($data);
        ManipulationActivity::noteManipulationAdmin( "Thêm Mới Phí Dịch Vụ ID : ".$servicecharge_id);
        $this->message("success",'Thêm Mới Phí Dịch Vụ ID : '.$servicecharge_id.' Thành Công!');
    }

    public function update_item($data){
        $this->update($data['servicecharge_id'],$data);
        ManipulationActivity::noteManipulationAdmin( "Cập Nhật Phí Dịch Vụ ( ID : ".$data['servicecharge_id'].")");
        $this->message("success",'Cập Nhật Phí Dịch Vụ ID '.$data['servicecharge_id'].' Thành Công!');
        return true;
    }

    public function move_bin($id){
        $this->delete($id);
        ManipulationActivity::noteManipulationAdmin( "Chuyển Phí Dịch Vụ( ID : ".$id.") Vào Thùng Rác");
    }


    public function count_bin(){
        $result = $this->model->onlyTrashed()->count();
        return $result;
    }
    public function getItemBinByPaginate($value)
    {
        $result = $this->model->onlyTrashed()->orderby('created_at', 'desc')->paginate($value);
        ManipulationActivity::noteManipulationAdmin( "Xem Phí Dịch Vụ Trong Thùng Rác");
        return $result;
    }
    // public function search_bin($key){
    //     if($key){
    //         $result = $this->model::onlyTrashed()->where(function ($query) use( $key ) {
    //             $query->where('servicecharge_name', 'like', '%' . $key . '%')
    //             ->orwhere('servicecharge_id', 'like', '%' . $key . '%');
    //         })->get();
            
    //         $output =  $this->output_item_bin($result);
    //         return $output;
    //     }else{
    //         $result =  $this->model::onlyTrashed()->get();
    //         $output = $this->output_item_bin($result);
    //         return $output;
    //     }
    // }

    public function restore_item($id){
        $result = $this->model->withTrashed()->find($id);
        $result->restore();
        ManipulationActivity::noteManipulationAdmin( "Khôi Phục Phí Dịch Vụ ( ID : ". $id.")");
        return true;
    }

    public function delete_item($id){
        $result = $this->model->withTrashed()->find($id);
        $result->forceDelete();
        ManipulationActivity::noteManipulationAdmin( "Xóa Vĩnh Viển Phí Dịch Vụ ( ID : ". $id.")");
        return true;
    }
    
    public function output_item($items){
        $output = '';
        foreach ($items as $key => $servicecharge){
            $output .= '
            <tr>
            <td>'.$servicecharge->servicecharge_id.'</td>
            <td>'.$servicecharge->hotel->hotel_name.'</td> 
            <td>';
            if ($servicecharge->servicecharge_condition == 1){
                 $output .= '
                    Tính Phí Theo %
                 ';
            }else{
                $output .= '
                    Tính Phí Theo Tiền
            ';
            }
            $output .= '                     
            </td>
            <td>';
            if ($servicecharge->servicecharge_condition == 1){
                 $output .= '
                    '.$servicecharge->servicecharge_fee.'%
                 ';
            }else{
                $output .= '
                '.number_format($servicecharge->servicecharge_fee,0,',','.').'đ
            ';
            }
            $output .= '                     
            </td>

            <td>
            <a href="'.URL('admin/servicecharge/edit-service-charge?servicecharge_id='.$servicecharge->servicecharge_id).'">
                <button type="button" class="btn-sm btn-gradient-info btn-icon-text">
                <i class="mdi mdi-delete-forever btn-icon-prepend"></i> Chỉnh Sửa </button>
            </a>
            </br>';
            if(Auth::user()->hasAnyRoles(['admin','manager'])) {
                $output .= '<button type="button" class="btn-sm btn-gradient-danger btn-icon-text btn-delete-item mt-2" data-item_id = "'. $servicecharge->servicecharge_id.'">
                <i class="mdi mdi-delete-forever btn-icon-prepend"></i> Xóa </button>';
            }
            $output .= '
            </td>
        </tr>
            ';
        }
        return $output;
    }
    public function output_item_bin($items)
    {
        $output = '';
        foreach ($items as $key => $servicecharge) {
            $output .= '
            <tr>
            <td>'.$servicecharge->servicecharge_id.'</td>
            <td>'.$servicecharge->hotel->hotel_name.'</td> 
            <td>';
            if ($servicecharge->servicecharge_condition == 1){
                 $output .= '
                    Tính Phí Theo %
                 ';
            }else{
                $output .= '
                    Tính Phí Theo Tiền
            ';
            }
            $output .= '                     
            </td>
            <td>';
            if ($servicecharge->servicecharge_condition == 1){
                 $output .= '
                    '.$servicecharge->servicecharge_fee.'%
                 ';
            }else{
                $output .= '
                '.number_format($servicecharge->servicecharge_fee,0,',','.').'đ
            ';
            }
            $output .= '                     
            </td>
            <td>'.$servicecharge->deleted_at.'</td> 

            <td>
                <button type="button" class="btn-sm btn-gradient-success btn-icon-text btn-restore-item" data-item_id = "'. $servicecharge->servicecharge_id.'">
                <i class="mdi mdi-backup-restore btn-icon-prepend"></i> Khôi Phục </button>
                </br>
                <button type="button" class="btn-sm btn-gradient-danger btn-icon-text mt-3 btn-delete-item" data-item_id = "'. $servicecharge->servicecharge_id.'">
                <i class="mdi mdi-delete-forever btn-icon-prepend"></i> Xóa </button>
            </td>
        </tr>
        ';
        }
        return $output;
    }

    public function message($type,$content){
        $message = array(
            "type" => "$type",
            "content" => "$content",
        ); 
        session()->flash('message', $message);
    }


}