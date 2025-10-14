<?php
namespace App\Repositories\BrandRepository;

use App\Repositories\BaseRepository;
use App\Models\ManipulationActivity;
use Auth;
class BrandRepository extends BaseRepository implements BrandRepositoryInterface
{
    //lấy model tương ứng
    public function getModel()
    {
        return \App\Models\Brand::class;
    }
    public function getAllByPaginate($value){
        ManipulationActivity::noteManipulationAdmin( "Xem Danh Sách Thương Hiệu");
        return $this->model->paginate($value);
    }
    public function searchIDorName($key){
        ManipulationActivity::noteManipulationAdmin( "Tìm Kiếm Danh Sách Thương Hiệu");
        if($key){
            $result = $this->model::where('brand_id', 'like', '%' . $key . '%')->orwhere('brand_name', 'like', '%' . $key . '%')->get();
            return $result;
        }else{
            $result = $this->model->take(3)->get();
            return $result;
        }
    }
    public function insert_item($data){
        unset($data['_token']);
        $brand_id = $this->model->insertGetId($data);
        ManipulationActivity::noteManipulationAdmin( "Thêm Mới Thương Hiệu ID : ".$brand_id);
        $this->message("success",'Thêm Mới Thương Hiệu ID : '.$brand_id.' Thành Công!');
    }

    public function update_item($data){
        unset($data['_token']);
        $result = $this->update($data['brand_id'] , $data);
        ManipulationActivity::noteManipulationAdmin( "Cập Nhật Thương Hiệu ( ID : ".$data['brand_id'].")");
        $this->message("success",'Cập Nhật Thương Hiệu ID '.$data['brand_id'].' Thành Công!');
        return true;
    }

    public function update_status($data)
    {
        $this->update($data['brand_id'], $data);
        if($data['brand_status'] == 1 ){
            ManipulationActivity::noteManipulationAdmin( "Kích Hoạt Thương Hiệu( ID : ".$data['brand_id'].")");
        }else if($data['brand_status'] == 0){
            ManipulationActivity::noteManipulationAdmin( "Vô Hiệu Thương Hiệu ( ID : ".$data['brand_id'].")");
        }
        return true;
    }
    public function move_bin($id){
        $this->delete($id);
        ManipulationActivity::noteManipulationAdmin( "Chuyển Thương Hiệu( ID : ".$id.") Vào Thùng Rác");
    }


    public function count_bin(){
        $result = $this->model->onlyTrashed()->count();
        return $result;
    }
    public function getItemBinByPaginate($value)
    {
        $result = $this->model->onlyTrashed()->orderby('created_at', 'desc')->paginate($value);
        ManipulationActivity::noteManipulationAdmin( "Xem Thương Hiệu Trong Thùng Rác");
        return $result;
    }
    public function search_bin($key){
        if($key){
            $result = $this->model::onlyTrashed()->where('brand_name', 'like', '%' . $key . '%')->orwhere('brand_id', 'like', '%' . $key . '%')->get();
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
        ManipulationActivity::noteManipulationAdmin( "Khôi Phục Thương Hiệu ( ID : ". $id.")");
        return true;
    }
    public function delete_item($id){
        $result = $this->model->withTrashed()->find($id);
        $result->forceDelete();
        ManipulationActivity::noteManipulationAdmin( "Xóa Vĩnh Viển Thương Hiệu ( ID : ". $id.")");
        return true;
    }
    
    public function output_item($items){
        $output = '';
        foreach ($items as $key => $brand){
            $output .= '
            <tr>
            <td>'.$brand->brand_id.'</td>
            <td>'.$brand->brand_name.'</td> 
            <td>'.$brand->brand_desc.'</td>
            <td>';
            if ($brand->brand_status == 1){
                 $output .= '
                 <span class = "update-status" data-item_id = "'.$brand->brand_id.'" data-item_status = "0">
                 <i style="color: rgb(52, 211, 52); font-size: 30px"
                 class="mdi mdi-toggle-switch"></i>
                 </span>
                 ';
            }else{
                $output .= '
                <span class = "update-status" data-item_id = "'.$brand->brand_id.'" data-item_status = "1" >
                <i style="color: rgb(196, 203, 196);font-size: 30px"
                class="mdi mdi-toggle-switch-off"></i>
                </span>'
            ;
            }
            $output .= '                     
            </td>

            <td>
            <a href="'.URL('admin/brand/edit-brand?brand_id=' . $brand->brand_id).'">
                <button type="button" class="btn-sm btn-gradient-info btn-icon-text">
                <i class="mdi mdi-delete-forever btn-icon-prepend"></i> Chỉnh Sửa </button>
            </a>
            </br>';
            if(Auth::user()->hasAnyRoles(['admin','manager'])) {
                $output .= '<button type="button" class="btn-sm btn-gradient-danger btn-icon-text btn-delete-item mt-2" data-item_id = "'. $brand->brand_id.'">
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
        foreach ($items as $key => $brand) {
            $output .= '
            <tr>
            <td>'.$brand->brand_id.'</td>
            <td>'.$brand->brand_name.'</td> 
            <td>'.$brand->brand_desc.'</td>
            <td>'.$brand->deleted_at.'</td>
            <td>
                <button type="button" class="btn-sm btn-gradient-success btn-icon-text btn-restore-item" data-item_id = "'. $brand->brand_id.'">
                <i class="mdi mdi-backup-restore btn-icon-prepend"></i> Khôi Phục </button>
                </br>
                <button type="button" class="btn-sm btn-gradient-danger btn-icon-text mt-3 btn-delete-item" data-item_id = "'. $brand->brand_id.'">
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