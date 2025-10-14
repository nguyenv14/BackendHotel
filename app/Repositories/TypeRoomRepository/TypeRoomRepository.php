<?php
namespace App\Repositories\TypeRoomRepository;

use App\Models\Hotel;
use App\Models\ManipulationActivity;
use App\Models\Room;
use App\Repositories\BaseRepository;
use Auth;

class TypeRoomRepository extends BaseRepository implements TypeRoomRepositoryInterface
{
    //lấy model tương ứng
    public function getModel()
    {
        return \App\Models\TypeRoom::class;
    }
    public function getHotel($id)
    {
        return Hotel::where('hotel_id', $id)->first();
    }
    public function getAllRoomByHotel($id)
    {
        return Room::where('hotel_id', $id)->get();
    }
    public function getAllByPaginate($id, $value)
    {
        ManipulationActivity::noteManipulationAdmin("Xem Danh Sách Lựa Chọn Của Lựu Chọn Phòng");
        return $this->model->where('room_id', $id)->paginate($value);
    }

    public function insert_item($data)
    {
        unset($data['_token']);
        unset($data['hotel_id']);
        $type_room_id = $this->model->insertGetId($data);
        ManipulationActivity::noteManipulationAdmin("Thêm Mới Lựu Chọn Phòng ID : " . $type_room_id);
        $this->message("success", 'Thêm Mới Lựu Chọn Phòng ID : ' . $type_room_id . ' Thành Công!');
    }

    public function update_item($data)
    {
        $result = $this->update($data['type_room_id'], $data);
        ManipulationActivity::noteManipulationAdmin("Cập Nhật Lựu Chọn Phòng ( ID : " . $data['type_room_id'] . ")");
        $this->message("success", 'Cập Nhật Lựu Chọn Phòng ID ' . $data['type_room_id'] . ' Thành Công!');
        return true;
    }

    public function searchIDorName($data)
    {
        ManipulationActivity::noteManipulationAdmin("Tìm Kiếm Danh Sách Lựu Chọn Phòng");
        if ($data['key_sreach'] != '') {
            $result = $this->model::where('type_room_id', 'like', '%' . $data['key_sreach']  . '%')->orwhere('type_room_price', 'like', '%' . $data['key_sreach'] . '%')->where('room_id',$data['room_id'])->get();
            return $result;
        } else {
            $result = $this->model::where('room_id',$data['room_id'])->take(5)->get();
            return $result;
        }
    }

    public function update_status($data)
    {
        $this->update($data['type_room_id'], $data);
        if ($data['type_room_status'] == 1) {
            ManipulationActivity::noteManipulationAdmin("Kích Hoạt Lựu Chọn Phòng( ID : " . $data['type_room_id'] . ")");
        } else if ($data['type_room_status'] == 0) {
            ManipulationActivity::noteManipulationAdmin("Vô Hiệu Lựu Chọn Phòng ( ID : " . $data['type_room_id'] . ")");
        }
        return true;
    }
    public function move_bin($id)
    {
        $this->delete($id);
        ManipulationActivity::noteManipulationAdmin("Chuyển Lựu Chọn Phòng( ID : " . $id . ") Vào Thùng Rác");
    }

    public function count_bin($id)
    {
        $result = $this->model->onlyTrashed()->where('room_id',$id)->count();
        return $result;
    }
    public function getItemBinByPaginate($id,$value)
    {
        $result = $this->model->onlyTrashed()->where('room_id',$id)->orderby('created_at', 'desc')->paginate($value);
        ManipulationActivity::noteManipulationAdmin("Xem Lựu Chọn Phòng Trong Thùng Rác");
        return $result;
    }
    public function search_bin($data)
    {
        ManipulationActivity::noteManipulationAdmin("Tìm Kiếm Danh Sách Lựu Chọn Phòng Trong Thùng Rác");
        if ($data['key_sreach'] != '') {
            $result = $this->model::onlyTrashed()->where('room_id',$data['room_id'])->where('type_room_id', 'like', '%' . $data['key_sreach']  . '%')->get();
            $output = $this->output_item_bin($result);
            return $output;
        } else {
            $result = $this->model::onlyTrashed()->where('room_id',$data['room_id'])->take(5)->get();
            $output = $this->output_item_bin($result);
            return $output;
        }

    }
    public function restore_item($id)
    {
        $result = $this->model->withTrashed()->find($id);
        $result->restore();
        ManipulationActivity::noteManipulationAdmin("Khôi Phục Lựu Chọn Phòng ( ID : " . $id . ")");
        return true;
    }
    public function delete_item($id)
    {
        $result = $this->model->withTrashed()->find($id);
        $result->forceDelete();
        ManipulationActivity::noteManipulationAdmin("Xóa Vĩnh Viển Lựu Chọn Phòng ( ID : " . $id . ")");
        return true;
    }

    public function output_item($items)
    {
        $output = '';
        foreach ($items as $key => $typeroom) {
            $output .= '
            <tr>
            <td>' . $typeroom->type_room_id . '</td>
            <td> Lựa Chọn ' . ($key + 1) . '</td>
            <td>';
            if ($typeroom->type_room_bed == 1) {
                $output .= '
                1 Giường Đơn
                 ';
            } else if ($typeroom->type_room_bed == 2) {
                $output .= '
                2 Giường Đơn
                ';
            } else if ($typeroom->type_room_bed == 3) {
                $output .= '
                1 Giường Hoặc 2 Giường Đơn
            ';
            }
            $output .= '
            </td>
            <td>' . number_format($typeroom->type_room_price, 0, ',', '.') . 'đ</td>
            <td>';
            if ($typeroom->type_room_condition == 0) {
                $output .= '
                Không Giảm Giá
                 ';
            } else {
                $output .= '
                Giảm Giá ' . $typeroom->type_room_price_sale . '%'
                ;
            }
            $output .= '
            </td>
            <td>';
            if ($typeroom->type_room_condition == 0) {
                $output .= '
               Không Có
                 ';
            } else if ($typeroom->type_room_condition == 1) {
                $price_sale = $typeroom->type_room_price - (($typeroom->type_room_price / 100 ) * $typeroom->type_room_price_sale);
                $output .= number_format($price_sale, 0, ',', '.').'đ' ;
            }
            $output .= '
            </td>
            <td>' . $typeroom->type_room_quantity .'</td>
            <td>';
            if ($typeroom->type_room_status == 1) {
                $output .= '
                 <span class = "update-status" data-item_id = "' . $typeroom->type_room_id . '" data-item_status = "0">
                 <i style="color: rgb(52, 211, 52); font-size: 30px"
                 class="mdi mdi-toggle-switch"></i>
                 </span>
                 ';
            } else {
                $output .= '
                <span class = "update-status" data-item_id = "' . $typeroom->type_room_id . '" data-item_status = "1" >
                <i style="color: rgb(196, 203, 196);font-size: 30px"
                class="mdi mdi-toggle-switch-off"></i>
                </span>'
                ;
            }
            $output .= '
            </td>

            <td>
            <a href="'.URL('admin/hotel/manager/room/typeroom/edit-typeroom?type_room_id=' . $typeroom->type_room_id).'&hotel_id='.$typeroom->room->hotel_id.'">
                <button type="button" class="btn-sm btn-gradient-info btn-icon-text">
                <i class="mdi mdi-delete-forever btn-icon-prepend"></i> Chỉnh Sửa </button>
            </a>
            </br>';
            if (Auth::user()->hasAnyRoles(['admin', 'manager'])) {
                $output .= '<button type="button" class="btn-sm btn-gradient-danger btn-icon-text btn-delete-item mt-2" data-item_id = "' . $typeroom->type_room_id . '">
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
        foreach ($items as $key => $typeroom) {
            $output .= '
            <tr>
            <td>' . $typeroom->type_room_id . '</td>
            <td> Lựa Chọn ' . ($key + 1) . '</td>
            <td>';
            if ($typeroom->type_room_bed == 1) {
                $output .= '
                1 Giường Đơn
                 ';
            } else if ($typeroom->type_room_bed == 2) {
                $output .= '
                2 Giường Đơn
                ';
            } else if ($typeroom->type_room_bed == 3) {
                $output .= '
                1 Giường Hoặc 2 Giường Đơn
            ';
            }
            $output .= '
            </td>
            <td>' . number_format($typeroom->type_room_price, 0, ',', '.') . 'đ</td>
            <td>';
            if ($typeroom->type_room_condition == 0) {
                $output .= '
                Không Giảm Giá
                 ';
            } else {
                $output .= '
                Giảm Giá ' . $typeroom->type_room_price_sale . '%'
                ;
            }
            $output .= '
            </td>
            <td>';
            if ($typeroom->type_room_condition == 0) {
                $output .= '
               Không Có
                 ';
            } else if ($typeroom->type_room_condition == 1) {
                $price_sale = $typeroom->type_room_price - (($typeroom->type_room_price / 100 ) * $typeroom->type_room_price_sale);
                $output .= number_format($price_sale, 0, ',', '.').'đ' ;
            }
            $output .= '
            </td>
            <td>' . $typeroom->type_room_quantity .'</td>
            <td>
            <button type="button" class="btn-sm btn-gradient-success btn-icon-text btn-restore-item" data-item_id = "' . $typeroom->type_room_id . '">
            <i class="mdi mdi-backup-restore btn-icon-prepend"></i> Khôi Phục </button>
            </br>
            <button type="button" class="btn-sm btn-gradient-danger btn-icon-text mt-3 btn-delete-item" data-item_id = "' . $typeroom->type_room_id . '">
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
