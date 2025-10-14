<?php
namespace App\Repositories\AreaRepository;

use App\Repositories\BaseRepository;
use App\Models\ManipulationActivity;
use Auth;
class AreaRepository extends BaseRepository implements AreaRepositoryInterface
{
    //lấy model tương ứng
    public function getModel()
    {
        return \App\Models\Area::class;
    }
    public function getAllByPaginate($value){
        ManipulationActivity::noteManipulationAdmin( "Xem Danh Sách Khu Vực");
        return $this->model->paginate($value);
    }
    public function searchIDorName($key){
        ManipulationActivity::noteManipulationAdmin( "Tìm Kiếm Danh Sách Khu Vực");
        if($key){
            $result = $this->model::where('area_id', 'like', '%' . $key . '%')->orwhere('area_name', 'like', '%' . $key . '%')->get();
            return $result;
        }else{
            $result = $this->model->take(3)->get();
            return $result;
        }
    }
    public function insert_item($data,$get_image){
        unset($data['_token']);
        if ($get_image != null) {
            $get_image_name = $get_image->getClientOriginalName(); /* Lấy Tên File */
            $image_name = current(explode('.', $get_image_name)); /* VD Tên File Là nhan.jpg thì hàm explode dựa vào dấm . để phân tách thành 2 chuổi là nhan và jpg , còn hàm current để chuổi đầu , hàm end thì lấy cuối */
            $new_image = $image_name . rand(0, 99) . '.' . $get_image->getClientOriginalExtension(); /* getClientOriginalExtension() hàm lấy phần mở rộng của ảnh */
            $get_image->move('public/fontend/assets/img/area/', $new_image);
            $data['area_image'] = $new_image;
        } 
        $area_id = $this->model->insertGetId($data);
        ManipulationActivity::noteManipulationAdmin( "Thêm Mới Khu Vực ID : ".$area_id);
        $this->message("success",'Thêm Mới Khu Vực ID : '.$area_id.' Thành Công!');
    }

    public function update_item($data,$get_image){
        $area = $this->model->find($data['area_id']);
        if ($get_image != null) {
            $get_image_name = $get_image->getClientOriginalName(); /* Lấy Tên File */
            $image_name = current(explode('.', $get_image_name)); /* VD Tên File Là nhan.jpg thì hàm explode dựa vào dấm . để phân tách thành 2 chuổi là nhan và jpg , còn hàm current để chuổi đầu , hàm end thì lấy cuối */
            $new_image = $image_name . rand(0, 99) . '.' . $get_image->getClientOriginalExtension(); /* getClientOriginalExtension() hàm lấy phần mở rộng của ảnh */
            $get_image->move('public/fontend/assets/img/area/', $new_image);
            unlink('public/fontend/assets/img/area/'. $area->area_image);
            $area['area_image'] = $new_image;
        } 
        $area->area_id = $data['area_id'];
        $area->area_name = $data['area_name'];
        $area->area_status = $data['area_status'];
        $area->area_desc = $data['area_desc'];
        $area->save();
        ManipulationActivity::noteManipulationAdmin( "Cập Nhật Khu Vực ( ID : ".$data['area_id'].")");
        $this->message("success",'Cập Nhật Khu Vực ID '.$data['area_id'].' Thành Công!');
        return true;
    }

    public function update_status($data)
    {
        $this->update($data['area_id'], $data);
        if($data['area_status'] == 1 ){
            ManipulationActivity::noteManipulationAdmin( "Kích Hoạt Khu Vực( ID : ".$data['area_id'].")");
        }else if($data['area_status'] == 0){
            ManipulationActivity::noteManipulationAdmin( "Vô Hiệu Khu Vực ( ID : ".$data['area_id'].")");
        }
        return true;
    }
    public function move_bin($id){
        $this->delete($id);
        ManipulationActivity::noteManipulationAdmin( "Chuyển Khu Vực( ID : ".$id.") Vào Thùng Rác");
    }


    public function count_bin(){
        $result = $this->model->onlyTrashed()->count();
        return $result;
    }
    public function getItemBinByPaginate($value)
    {
        $result = $this->model->onlyTrashed()->orderby('created_at', 'desc')->paginate($value);
        ManipulationActivity::noteManipulationAdmin( "Xem Khu Vực Trong Thùng Rác");
        return $result;
    }
    public function search_bin($key){
        if($key){
            $result = $this->model::onlyTrashed()->where('area_name', 'like', '%' . $key . '%')->orwhere('area_id', 'like', '%' . $key . '%')->get();
            $output =  $this->output_item_bin($result);
            return $output;
        }else{
            $result =  $this->model::onlyTrashed()->get();
            $output = $this->output_item_bin($result);
            return $output;
        }
       
    }
    public function restore_item($id){
        $result = $this->model->withTrashed()->find($id);
        $result->restore();
        ManipulationActivity::noteManipulationAdmin( "Khôi Phục Khu Vực ( ID : ". $id.")");
        return true;
    }
    public function delete_item($id){
        $result = $this->model->withTrashed()->find($id);
        $result->forceDelete();
        ManipulationActivity::noteManipulationAdmin( "Xóa Vĩnh Viển Khu Vực ( ID : ". $id.")");
        return true;
    }
    
    public function output_item($items){
        $output = '';
        foreach ($items as $key => $area){
            $output .= '
            <tr>
            <td>'.$area->area_id.'</td>
            <td>'.$area->area_name.'</td> 
            <td><img style="object-fit: cover" width="40px" height="20px"src="'.URL('public/fontend/assets/img/area/'.$area->area_image).'"alt=""></td>
            <td>'.$area->area_desc.'</td>
            <td>';
            if ($area->area_status == 1){
                 $output .= '
                 <span class = "update-status" data-item_id = "'.$area->area_id.'" data-item_status = "0">
                 <i style="color: rgb(52, 211, 52); font-size: 30px"
                 class="mdi mdi-toggle-switch"></i>
                 </span>
                 ';
            }else{
                $output .= '
                <span class = "update-status" data-item_id = "'.$area->area_id.'" data-item_status = "1" >
                <i style="color: rgb(196, 203, 196);font-size: 30px"
                class="mdi mdi-toggle-switch-off"></i>
                </span>'
            ;
            }
            $output .= '                     
            </td>

            <td>
            <a href="'.URL('admin/area/edit-area?area_id=' . $area->area_id).'">
                <button type="button" class="btn-sm btn-gradient-info btn-icon-text">
                <i class="mdi mdi-delete-forever btn-icon-prepend"></i> Chỉnh Sửa </button>
            </a>
            </br>';
            if(Auth::user()->hasAnyRoles(['admin','manager'])) {
                $output .= '<button type="button" class="btn-sm btn-gradient-danger btn-icon-text btn-delete-item mt-2" data-item_id = "'. $area->area_id.'">
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
        foreach ($items as $key => $area) {
            $output .= '
            <tr>
            <td>'.$area->area_id.'</td>
            <td>'.$area->area_name.'</td> 
            <td><img style="object-fit: cover" width="40px" height="20px"src="'.URL('public/fontend/assets/img/area/'.$area->area_image).'"alt=""></td>
            <td>'.$area->area_desc.'</td>
            <td>'.$area->deleted_at.'</td>
            <td>
                <button type="button" class="btn-sm btn-gradient-success btn-icon-text btn-restore-item" data-item_id = "'. $area->area_id.'">
                <i class="mdi mdi-backup-restore btn-icon-prepend"></i> Khôi Phục </button>
                </br>
                <button type="button" class="btn-sm btn-gradient-danger btn-icon-text mt-3 btn-delete-item" data-item_id = "'. $area->area_id.'">
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