<?php
namespace App\Repositories\RoomRepository;

use App\Repositories\BaseRepository;
use App\Models\ManipulationActivity;
use App\Models\Hotel;
use Auth;
class RoomRepository extends BaseRepository implements RoomRepositoryInterface
{
    //lấy model tương ứng
    public function getModel()
    {
        return \App\Models\Room::class;
    }
    public function getHotel($id){
        return Hotel::where('hotel_id',$id)->first();
    }
    public function getAllByPaginate($id,$value){
        ManipulationActivity::noteManipulationAdmin( "Xem Danh Sách Phòng");
        return $this->model->where('hotel_id',$id)->paginate($value);
    }
    public function searchIDorName($data){
        ManipulationActivity::noteManipulationAdmin( "Tìm Kiếm Danh Sách Phòng");
        if($data['key_search']){
            $result =  $this->model::where(function ($query) use( $data) {
                $query->where('room_id', 'like', '%' . $data['key_search'] . '%')
                ->orwhere('room_name', 'like', '%' . $data['key_search'] . '%');
            })->where(function ($query) use( $data){
                $query->where('hotel_id', $data['hotel_id']);
            })->get(); 
            return $result;
        }else{
            $result = $this->model::where('hotel_id', $data['hotel_id'])->take(5)->get();
            return $result;
        }
    }
    public function insert_item($data){
        unset($data['_token']);
        $room_id = $this->model->insertGetId($data);
        ManipulationActivity::noteManipulationAdmin( "Thêm Mới Phòng ID : ".$room_id);
        $this->message("success",'Thêm Mới Phòng ID : '.$room_id.' Thành Công!');
    }

    public function update_item($data){
        unset($data['_token']);
        $result = $this->update($data['room_id'] , $data);
        ManipulationActivity::noteManipulationAdmin( "Cập Nhật Phòng ( ID : ".$data['room_id'].")");
        $this->message("success",'Cập Nhật Phòng ID '.$data['room_id'].' Thành Công!');
        return true;
    }

    public function update_status($data)
    {
        $this->update($data['room_id'], $data);
        if($data['room_status'] == 1 ){
            ManipulationActivity::noteManipulationAdmin( "Kích Hoạt Phòng( ID : ".$data['room_id'].")");
        }else if($data['room_status'] == 0){
            ManipulationActivity::noteManipulationAdmin( "Vô Hiệu Phòng ( ID : ".$data['room_id'].")");
        }
        return true;
    }
    public function move_bin($id){
        $this->delete($id);
        ManipulationActivity::noteManipulationAdmin( "Chuyển Phòng( ID : ".$id.") Vào Thùng Rác");
    }


    public function count_bin($id){
        $result = $this->model->onlyTrashed()->where('hotel_id' , $id)->count();
        return $result;
    }
    public function getItemBinByPaginate($id,$value)
    {
        $result = $this->model->onlyTrashed()->where('hotel_id',$id)->orderby('created_at', 'desc')->paginate($value);
        ManipulationActivity::noteManipulationAdmin( "Xem Phòng Trong Thùng Rác");
        return $result;
    }
    public function search_bin($data){
        if($data['key_search']){
            $result =  $this->model::onlyTrashed()->where(function ($query) use( $data) {
                $query->where('room_id', 'like', '%' . $data['key_search'] . '%')
                ->orwhere('room_name', 'like', '%' . $data['key_search'] . '%');
            })->where(function ($query) use( $data){
                $query->where('hotel_id', $data['hotel_id']);
            })->get(); 
            $output =  $this->output_item_bin($result);
            return $output;
        }else{
            $result =  $this->model::onlyTrashed()->where('hotel_id', $data['hotel_id'])->get();
            $output = $this->output_item_bin($result);
            return $output;
        }
       
    }
    public function restore_item($id){
        $result = $this->model->withTrashed()->find($id);
        $result->restore();
        ManipulationActivity::noteManipulationAdmin( "Khôi Phục Phòng ( ID : ". $id.")");
        return true;
    }
    public function delete_item($id){
        $result = $this->model->withTrashed()->find($id);
        $result->forceDelete();
        ManipulationActivity::noteManipulationAdmin( "Xóa Vĩnh Viển Phòng ( ID : ". $id.")");
        return true;
    }
    
    public function output_item($items){
        $output = '';
        foreach ($items as $key => $room){
            $output .= '
            <tr>
            <td>'.$room->room_id.'</td>
            <td>'.$room->room_name.'</td> 
            <td>'.$room->room_amount_of_people.'</td>
            <td>'.$room->room_acreage.' m<sup>2</sup></td>
            <td>'.$room->room_view.'</td>
            <td>';
            if ($room->room_status == 1){
                 $output .= '
                 <span class = "update-status" data-item_id = "'.$room->room_id.'" data-item_status = "0">
                 <i style="color: rgb(52, 211, 52); font-size: 30px"
                 class="mdi mdi-toggle-switch"></i>
                 </span>
                 ';
            }else{
                $output .= '
                <span class = "update-status" data-item_id = "'.$room->room_id.'" data-item_status = "1" >
                <i style="color: rgb(196, 203, 196);font-size: 30px"
                class="mdi mdi-toggle-switch-off"></i>
                </span>'
            ;
            }
            $output .= '                     
            </td>

            <td>
            <a href="'.URL('admin/hotel/manager/room/typeroom/all-typeroom?room_id=' . $room->room_id).'&hotel_id='.$room->hotel_id.'">
            <button type="button" class="btn-sm btn-gradient-info btn-icon-text">
            <i class="mdi mdi-delete-forever btn-icon-prepend"></i> Danh Sách Lựa Chọn</button>
            </a>
            </br>
            </br>
            <a href="'.URL('admin/hotel/manager/room/edit-room?room_id=' . $room->room_id).'&hotel_id='.$room->hotel_id.'">
                <button type="button" class="btn-sm btn-gradient-info btn-icon-text">
                <i class="mdi mdi-delete-forever btn-icon-prepend"></i> Chỉnh Sửa </button>
            </a>
            </br>
            </br>';
            if(Auth::user()->hasAnyRoles(['admin','manager'])) {
                $output .= '<button type="button" class="btn-sm btn-gradient-danger btn-icon-text btn-delete-item" data-item_id = "'. $room->room_id.'">
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
        foreach ($items as $key => $room) {
            $output .= '
            <tr>
            <td>'.$room->room_id.'</td>
            <td>'.$room->room_name.'</td> 
            <td>'.$room->room_amount_of_people.'</td>
            <td>'.$room->room_acreage.' m<sup>2</sup></td>
            <td>'.$room->room_view.'</td>
            <td>
                <button type="button" class="btn-sm btn-gradient-success btn-icon-text btn-restore-item" data-item_id = "'. $room->room_id.'">
                <i class="mdi mdi-backup-restore btn-icon-prepend"></i> Khôi Phục </button>
                </br>
                <button type="button" class="btn-sm btn-gradient-danger btn-icon-text mt-3 btn-delete-item" data-item_id = "'. $room->room_id.'">
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