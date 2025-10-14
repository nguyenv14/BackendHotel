<?php
namespace App\Repositories\ManagerHotelRepository;

use App\Repositories\BaseRepository;
use App\Models\ManipulationActivity;
use App\Models\Area;
use App\Models\Brand;
use Auth;
class ManagerHotelRepository extends BaseRepository implements ManagerHotelRepositoryInterface
{
    //lấy model tương ứng
    public function getModel()
    {
        return \App\Models\Hotel::class;
    }
    public function getAllByPaginate($value){
        ManipulationActivity::noteManipulationAdmin( "Xem Danh Sách Khách Sạn");
        return $this->model->paginate($value);
    }
    public function getArea(){
        return Area::get();
    }
    public function getBrand(){
        return Brand::get();
    }
    public function searchIDorName($key){
        ManipulationActivity::noteManipulationAdmin( "Tìm Kiếm Danh Sách Khách Sạn");
        if($key){
            $result = $this->model::where('hotel_id', 'like', '%' . $key . '%')->orwhere('hotel_name', 'like', '%' . $key . '%')->get();
            return $result;
        }else{
            $result = $this->model->take(3)->get();
            return $result;
        }
    }
    public function insert_item($data,$get_image){
        unset($data['_token']);
        
        // Tách data hotel và manager
        $hotel_data = [
            'hotel_name' => $data['hotel_name'],
            'hotel_rank' => $data['hotel_rank'],
            'hotel_type' => $data['hotel_type'],
            'brand_id' => $data['brand_id'],
            'area_id' => $data['area_id'],
            'hotel_placedetails' => $data['hotel_placedetails'],
            'hotel_linkplace' => $data['hotel_linkplace'],
            'hotel_jfameplace' => $data['hotel_jfameplace'],
            'hotel_desc' => $data['hotel_desc'],
            'hotel_tag_keyword' => $data['hotel_tag_keyword'],
            'hotel_status' => $data['hotel_status']
        ];
        
        $manager_data = [
            'manager_name' => $data['manager_name'] ?? null,
            'manager_email' => $data['manager_email'] ?? null,
            'manager_phone' => $data['manager_phone'] ?? null,
            'manager_password' => $data['manager_password'] ?? null,
            'manager_password_confirmation' => $data['manager_password_confirmation'] ?? null
        ];
        
        if ($get_image != null) {
            $get_image_name = $get_image->getClientOriginalName(); /* Lấy Tên File */
            $image_name = current(explode('.', $get_image_name)); /* VD Tên File Là nhan.jpg thì hàm explode dựa vào dấm . để phân tách thành 2 chuổi là nhan và jpg , còn hàm current để chuổi đầu , hàm end thì lấy cuối */
            $new_image = $image_name . rand(0, 99) . '.' . $get_image->getClientOriginalExtension(); /* getClientOriginalExtension() hàm lấy phần mở rộng của ảnh */
            $get_image->move('public/fontend/assets/img/hotel/', $new_image);
            $hotel_data['hotel_image'] = $new_image;
        } 
        
        $hotel_id = $this->model->insertGetId($hotel_data);
        
        // Tạo Hotel Manager nếu có thông tin manager
        if (isset($manager_data['manager_name']) && isset($manager_data['manager_email'])) {
            $manager_created = $this->createHotelManager($manager_data, $hotel_id);
            if (!$manager_created) {
                // Nếu tạo manager thất bại, xóa hotel đã tạo
                $this->model->find($hotel_id)->delete();
                return false;
            }
        }
        
        ManipulationActivity::noteManipulationAdmin( "Thêm Mới Khách Sạn ID : ".$hotel_id);
        $this->message("success",'Thêm Mới Khách Sạn ID : '.$hotel_id.' Thành Công!');
    }

    private function createHotelManager($data, $hotel_id) {
        // Kiểm tra email đã tồn tại chưa
        $existing_admin = \App\Models\Admin::where('admin_email', $data['manager_email'])->first();
        if ($existing_admin) {
            $this->message("error", "Email quản lý đã tồn tại trong hệ thống!");
            return false;
        }

        // Tạo Admin (Hotel Manager)
        $admin = new \App\Models\Admin();
        $admin->admin_name = $data['manager_name'];
        $admin->admin_email = $data['manager_email'];
        $admin->admin_phone = $data['manager_phone'];
        $admin->admin_password = md5($data['manager_password']);
        $admin->hotel_id = $hotel_id;
        $admin->save();

        // Gán role hotel_manager (id = 4)
        $hotel_manager_role = \App\Models\Roles::where('roles_name', 'hotel_manager')->first();
        if ($hotel_manager_role) {
            $admin->roles()->attach($hotel_manager_role);
        }

        ManipulationActivity::noteManipulationAdmin("Thêm Mới Hotel Manager ID: " . $admin->admin_id . " cho Khách Sạn ID: " . $hotel_id);
        return true;
    }

    public function update_item($data,$get_image){
        
        $hotel = $this->find($data['hotel_id']);
        if ($get_image != null) {
            $get_image_name = $get_image->getClientOriginalName(); /* Lấy Tên File */
            $image_name = current(explode('.', $get_image_name)); /* VD Tên File Là nhan.jpg thì hàm explode dựa vào dấm . để phân tách thành 2 chuổi là nhan và jpg , còn hàm current để chuổi đầu , hàm end thì lấy cuối */
            $new_image = $image_name . rand(0, 99) . '.' . $get_image->getClientOriginalExtension(); /* getClientOriginalExtension() hàm lấy phần mở rộng của ảnh */
            $get_image->move('public/fontend/assets/img/hotel/', $new_image);
            unlink('public/fontend/assets/img/hotel/'. $hotel->hotel_image);
            $data['hotel_image'] = $new_image;
        } 
        $this->update($data['hotel_id'] , $data);
        ManipulationActivity::noteManipulationAdmin( "Cập Nhật Khách Sạn ( ID : ".$data['hotel_id'].")");
        $this->message("success",'Cập Nhật Khách Sạn ID '.$data['hotel_id'].' Thành Công!');
        return true;
    }

    public function update_status($data)
    {
        $this->update($data['hotel_id'], $data);
        if($data['hotel_status'] == 1 ){
            ManipulationActivity::noteManipulationAdmin( "Kích Hoạt Khách Sạn( ID : ".$data['hotel_id'].")");
        }else if($data['hotel_status'] == 0){
            ManipulationActivity::noteManipulationAdmin( "Vô Hiệu Khách Sạn ( ID : ".$data['hotel_id'].")");
        }
        return true;
    }
    public function move_bin($id){
        $this->delete($id);
        ManipulationActivity::noteManipulationAdmin( "Chuyển Khách Sạn( ID : ".$id.") Vào Thùng Rác");
    }


    public function count_bin(){
        $result = $this->model->onlyTrashed()->count();
        return $result;
    }
    public function getItemBinByPaginate($value)
    {
        $result = $this->model->onlyTrashed()->orderby('created_at', 'desc')->paginate($value);
        ManipulationActivity::noteManipulationAdmin( "Xem Khách Sạn Trong Thùng Rác");
        return $result;
    }
    public function search_bin($key){
        if($key){
            $result = $this->model::onlyTrashed()->where('hotel_name', 'like', '%' . $key . '%')->orwhere('hotel_id', 'like', '%' . $key . '%')->get();
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
        ManipulationActivity::noteManipulationAdmin( "Khôi Phục Khách Sạn ( ID : ". $id.")");
        return true;
    }
    public function delete_item($id){
        $result = $this->model->withTrashed()->find($id);
        $result->forceDelete();
        ManipulationActivity::noteManipulationAdmin( "Xóa Vĩnh Viển Khách Sạn ( ID : ". $id.")");
        return true;
    }
    
    public function output_item($items){
        $output = '';
        foreach ($items as $key => $hotel){
            $output .= '
            <tr>
            <td>'.$hotel->hotel_id.'</td>
            <td>'.$hotel->hotel_name.'</td>
            '; 
            if($hotel->hotel_rank == 1){
                $output .='<td>1 <i class="mdi mdi-star"></i></td>';
            }else if($hotel->hotel_rank == 2){
                $output .='<td>2 <i class="mdi mdi-star"></i></td>';
            }else if($hotel->hotel_rank == 3){
                $output .='<td>3 <i class="mdi mdi-star"></i></td>';
            }else if($hotel->hotel_rank == 4){
                $output .='<td>4 <i class="mdi mdi-star"></i></td>';
            }else if($hotel->hotel_rank == 5){
                $output .='<td>5 <i class="mdi mdi-star"></i></td>';
            }

            if($hotel->hotel_type == 1){
                $output .='<td> Khách Sạn </td>';
            }else if($hotel->hotel_type == 2){
                $output .='<td> Khách Sạn Căn Hộ </td>';
            }else if($hotel->hotel_type == 3){
                $output .='<td> Khu Nghĩ Dưỡng</td>';
            }

            $output .='
            <td>'.$hotel->brand->brand_name.'</td>
            <td><img style="object-fit: cover" width="40px" height="20px"src="'.URL('public/fontend/assets/img/hotel/'.$hotel->hotel_image).'"alt=""></td>
           
            <td> Quận '.$hotel->area->area_name.'</td>
            

            <td>';
            if ($hotel->hotel_status == 1){
                 $output .= '
                 <span class = "update-status" data-item_id = "'.$hotel->hotel_id.'" data-item_status = "0">
                 <i style="color: rgb(52, 211, 52); font-size: 30px"
                 class="mdi mdi-toggle-switch"></i>
                 </span>
                 ';
            }else{
                $output .= '
                <span class = "update-status" data-item_id = "'.$hotel->hotel_id.'" data-item_status = "1" >
                <i style="color: rgb(196, 203, 196);font-size: 30px"
                class="mdi mdi-toggle-switch-off"></i>
                </span>'
            ;
            }
            $output .= '                     
            </td>

            <td>
            <a href="'.URL('admin/hotel/manager?hotel_id=' . $hotel->hotel_id).'">
                <button type="button" class="btn-sm btn-gradient-info btn-icon-text">
                <i class="mdi mdi-shield btn-icon-prepend"></i> Quản Lý </button>
            </a>
            </br>';
            if(Auth::user()->hasAnyRoles(['admin','manager'])) {
                $output .= '<button type="button" class="btn-sm btn-gradient-danger btn-icon-text btn-delete-item mt-2" data-item_id = "'. $hotel->hotel_id.'">
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
        foreach ($items as $key => $hotel) {
            $output .= '
            <tr>
            <td>'.$hotel->hotel_id.'</td>
            <td>'.$hotel->hotel_name.'</td>
            '; 
            if($hotel->hotel_rank == 1){
                $output .='<td>1 <i class="mdi mdi-star"></i></td>';
            }else if($hotel->hotel_rank == 2){
                $output .='<td>2 <i class="mdi mdi-star"></i></td>';
            }else if($hotel->hotel_rank == 3){
                $output .='<td>3 <i class="mdi mdi-star"></i></td>';
            }else if($hotel->hotel_rank == 4){
                $output .='<td>4 <i class="mdi mdi-star"></i></td>';
            }else if($hotel->hotel_rank == 5){
                $output .='<td>5 <i class="mdi mdi-star"></i></td>';
            }

            if($hotel->hotel_type == 1){
                $output .='<td> Khách Sạn </td>';
            }else if($hotel->hotel_type == 2){
                $output .='<td> Khách Sạn Căn Hộ </td>';
            }else if($hotel->hotel_type == 3){
                $output .='<td> Khu Nghĩ Dưỡng</td>';
            }

            $output .='
            <td>'.$hotel->brand->brand_name.'</td>
            <td><img style="object-fit: cover" width="40px" height="20px"src="'.URL('public/fontend/assets/img/hotel/'.$hotel->hotel_image).'"alt=""></td>
            <td>'.$hotel->area->area_name.'</td>
            <td>'.$hotel->deleted_at.'</td>
            <td>
                <button type="button" class="btn-sm btn-gradient-success btn-icon-text btn-restore-item" data-item_id = "'. $hotel->hotel_id.'">
                <i class="mdi mdi-backup-restore btn-icon-prepend"></i> Khôi Phục </button>
                </br>
                <button type="button" class="btn-sm btn-gradient-danger btn-icon-text mt-3 btn-delete-item" data-item_id = "'. $hotel->hotel_id.'">
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