<?php
namespace App\Repositories\FacilitiesHotelRepository;

use App\Models\ManipulationActivity;
use App\Repositories\BaseRepository;
use Auth;

class FacilitiesHotelRepository extends BaseRepository implements FacilitiesHotelRepositoryInterface
{
    //lấy model tương ứng
    public function getModel()
    {
        return \App\Models\FacilitiesHotel::class;
    }
    public function getAllByPaginate($value)
    {
        ManipulationActivity::noteManipulationAdmin("Xem Danh Sách Tiện Ích Khách Sạn");
        return $this->model->paginate($value);
    }
    public function searchIDorName($key)
    {
        ManipulationActivity::noteManipulationAdmin("Tìm Kiếm Danh Sách Tiện Ích Khách Sạn");
        if ($key) {
            $result = $this->model::where('facilitieshotel_id', 'like', '%' . $key . '%')->orwhere('facilitieshotel_name', 'like', '%' . $key . '%')->get();
            return $result;
        } else {
            $result = $this->model->take(5)->get();
            return $result;
        }
    }
    public function insert_item($data, $get_image)
    {
        unset($data['_token']);
        if ($get_image != null) {
            $get_image_name = $get_image->getClientOriginalName(); /* Lấy Tên File */
            $image_name = current(explode('.', $get_image_name)); /* VD Tên File Là nhan.jpg thì hàm explode dựa vào dấm . để phân tách thành 2 chuổi là nhan và jpg , còn hàm current để chuổi đầu , hàm end thì lấy cuối */
            $new_image = $image_name . rand(0, 99) . '.' . $get_image->getClientOriginalExtension(); /* getClientOriginalExtension() hàm lấy phần mở rộng của ảnh */
            $get_image->move('public/fontend/assets/img/facilitieshotel/', $new_image);
            $data['facilitieshotel_image'] = $new_image;
        }
        $facilitieshotel_id = $this->model->insertGetId($data);
        ManipulationActivity::noteManipulationAdmin("Thêm Mới Tiện Ích Khách Sạn ID : " . $facilitieshotel_id);
        $this->message("success", 'Thêm Mới Tiện Ích Khách Sạn ID : ' . $facilitieshotel_id . ' Thành Công!');
    }

    public function update_item($data, $get_image)
    {
        // unset($data['_token']);
        $facilitieshotel = $this->model->find($data['facilitieshotel_id']);
        if ($get_image != null) {
            $get_image_name = $get_image->getClientOriginalName(); /* Lấy Tên File */
            $image_name = current(explode('.', $get_image_name)); /* VD Tên File Là nhan.jpg thì hàm explode dựa vào dấm . để phân tách thành 2 chuổi là nhan và jpg , còn hàm current để chuổi đầu , hàm end thì lấy cuối */
            $new_image = $image_name . rand(0, 99) . '.' . $get_image->getClientOriginalExtension(); /* getClientOriginalExtension() hàm lấy phần mở rộng của ảnh */
            $get_image->move('public/fontend/assets/img/facilitieshotel/', $new_image);
            unlink('public/fontend/assets/img/facilitieshotel/' . $facilitieshotel->facilitieshotel_image);
            $data['facilitieshotel_image'] = $new_image;
        }
        $this->update($data['facilitieshotel_id'], $data);
        ManipulationActivity::noteManipulationAdmin("Cập Nhật Tiện Ích Khách Sạn ( ID : " . $data['facilitieshotel_id'] . ")");
        $this->message("success", 'Cập Nhật Tiện Ích Khách Sạn ID ' . $data['facilitieshotel_id'] . ' Thành Công!');
        return true;
    }

    public function update_status($data)
    {
        $this->update($data['facilitieshotel_id'], $data);
        if ($data['facilitieshotel_status'] == 1) {
            ManipulationActivity::noteManipulationAdmin("Kích Hoạt Tiện Ích Khách Sạn( ID : " . $data['facilitieshotel_id'] . ")");
        } else if ($data['facilitieshotel_status'] == 0) {
            ManipulationActivity::noteManipulationAdmin("Vô Hiệu Tiện Ích Khách Sạn ( ID : " . $data['facilitieshotel_id'] . ")");
        }
        return true;
    }
    public function move_bin($id)
    {
        $this->delete($id);
        ManipulationActivity::noteManipulationAdmin("Chuyển Tiện Ích Khách Sạn( ID : " . $id . ") Vào Thùng Rác");
    }

    public function count_bin()
    {
        $result = $this->model->onlyTrashed()->count();
        return $result;
    }
    public function getItemBinByPaginate($value)
    {
        $result = $this->model->onlyTrashed()->orderby('created_at', 'desc')->paginate($value);
        ManipulationActivity::noteManipulationAdmin("Xem Tiện Ích Khách Sạn Trong Thùng Rác");
        return $result;
    }
    public function search_bin($key)
    {
        if ($key) {
            $result = $this->model::onlyTrashed()->where(function ($query) use ($key) {
                $query->where('facilitieshotel_name', 'like', '%' . $key . '%')
                    ->orwhere('facilitieshotel_id', 'like', '%' . $key . '%');
            })->get();

            $output = $this->output_item_bin($result);
            return $output;
        } else {
            $result = $this->model::onlyTrashed()->get();
            $output = $this->output_item_bin($result);
            return $output;
        }

    }
    public function restore_item($id)
    {
        $result = $this->model->withTrashed()->find($id);
        $result->restore();
        ManipulationActivity::noteManipulationAdmin("Khôi Phục Tiện Ích Khách Sạn ( ID : " . $id . ")");
        return true;
    }
    public function delete_item($id)
    {
        $result = $this->model->withTrashed()->find($id);
        $result->forceDelete();
        ManipulationActivity::noteManipulationAdmin("Xóa Vĩnh Viển Tiện Ích Khách Sạn ( ID : " . $id . ")");
        return true;
    }

    public function output_item($items)
    {
        $output = '';
        foreach ($items as $key => $facilitieshotel) {
            $output .= '
            <tr>
            <td>' . $facilitieshotel->facilitieshotel_id . '</td>
            <td>' . $facilitieshotel->facilitieshotel_name . '</td>
            <td>';
            if ($facilitieshotel->facilitieshotel_group == 1) {
                $output .= '
                Hình thức thanh toán
                 ';
            } else if ($facilitieshotel->facilitieshotel_group == 2) {
                $output .= '
                Phương tiện đi lại
                ';
            } else if ($facilitieshotel->facilitieshotel_group == 3) {
                $output .= '
                Thể thao
            ';
            } else if ($facilitieshotel->facilitieshotel_group == 4) {
                $output .= '
                Dịch vụ lễ tân
                ';
            } else if ($facilitieshotel->facilitieshotel_group == 5) {
                $output .= '
                Internet
            ';
            } else if ($facilitieshotel->facilitieshotel_group == 6) {
                $output .= '
                Thư giãn và làm đẹp
            ';
            } else if ($facilitieshotel->facilitieshotel_group == 7) {
                $output .= '
                Dịch vụ lau dọn
            ';
            } else if ($facilitieshotel->facilitieshotel_group == 8) {
                $output .= '
                Hỗ trợ người khuyết tật
            ';
            } else if ($facilitieshotel->facilitieshotel_group == 9) {
                $output .= '
                Dịch vụ nhà hàng
            ';
            } else if ($facilitieshotel->facilitieshotel_group == 10) {
                $output .= '
                Dịch vụ cho doanh nhân
            ';
            } else if ($facilitieshotel->facilitieshotel_group == 11) {
                $output .= '    
                Tiện ích tổng quát
            ';
            } else if ($facilitieshotel->facilitieshotel_group == 12) {
                $output .= '
                Dịch vụ hồ bơi
            ';
            }
            $output .= '
            </td>

            <td><img style="object-fit: cover" width="40px" height="20px"src="' . URL('public/fontend/assets/img/facilitieshotel/' . $facilitieshotel->facilitieshotel_image) . '"alt=""></td>
            <td>' . $facilitieshotel->facilitieshotel_desc . '</td>
            <td>';
            if ($facilitieshotel->facilitieshotel_status == 1) {
                $output .= '
                <span class = "update-status" data-item_id = "'.$facilitieshotel->facilitieshotel_id.'" data-item_status = "0">
                <i style="color: rgb(52, 211, 52); font-size: 30px"
                class="mdi mdi-toggle-switch"></i>
                </span>
                 ';
            } else {
                $output .= '
                <span class = "update-status" data-item_id = "' . $facilitieshotel->facilitieshotel_id . '" data-item_status = "1" >
                <i style="color: rgb(196, 203, 196);font-size: 30px"
                class="mdi mdi-toggle-switch-off"></i>
                </span>'
                ;
            }
            $output .= '
            </td>

            <td>
            <a href="' . URL('admin/hotel/facilities/hotel/edit-hotel-facilities?facilitieshotel_id=' . $facilitieshotel->facilitieshotel_id) . '">
                <button type="button" class="btn-sm btn-gradient-info btn-icon-text">
                <i class="mdi mdi-delete-forever btn-icon-prepend"></i> Chỉnh Sửa </button>
            </a>
            </br>';
            if (Auth::user()->hasAnyRoles(['admin', 'manager'])) {
                $output .= '<button type="button" class="btn-sm btn-gradient-danger btn-icon-text btn-delete-item mt-2" data-item_id = "' . $facilitieshotel->facilitieshotel_id . '">
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
        foreach ($items as $key => $facilitieshotel) {
            $output .= '
            <tr>
            <td>' . $facilitieshotel->facilitieshotel_id . '</td>
            <td>' . $facilitieshotel->facilitieshotel_name . '</td>
            <td><img style="object-fit: cover" width="40px" height="20px"src="' . URL('public/fontend/assets/img/facilitieshotel/' . $facilitieshotel->facilitieshotel_image) . '"alt=""></td>
            <td>' . $facilitieshotel->facilitieshotel_desc . '</td>
            <td>' . $facilitieshotel->deleted_at . '</td>
            <td>
                <button type="button" class="btn-sm btn-gradient-success btn-icon-text btn-restore-item" data-item_id = "' . $facilitieshotel->facilitieshotel_id . '">
                <i class="mdi mdi-backup-restore btn-icon-prepend"></i> Khôi Phục </button>
                </br>
                <button type="button" class="btn-sm btn-gradient-danger btn-icon-text mt-3 btn-delete-item" data-item_id = "' . $facilitieshotel->facilitieshotel_id . '">
                <i class="mdi mdi-delete-forever btn-icon-prepend"></i> Xóa </button>
            </td>
        </tr>
        ';
        }
        return $output;
    }

    public function message($type, $content)
    {
        $message = array(
            "type" => "$type",
            "content" => "$content",
        );
        session()->flash('message', $message);
    }
}
