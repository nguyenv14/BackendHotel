<?php
namespace App\Repositories\GalleryRoomRepository;

use App\Models\Hotel;
use App\Models\ManipulationActivity;
use App\Models\Room;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Redirect;

class GalleryRoomRepository extends BaseRepository implements GalleryRoomRepositoryInterface
{
    //lấy model tương ứng
    public function getModel()
    {
        return \App\Models\GalleryRoom::class;
    }
    public function findHotel($id)
    {
        return Hotel::find($id);
    }
    public function findRoom($id)
    {
        return Room::find($id);
    }
    public function getAllRomByIDHotel($id)
    {
        return Room::where('hotel_id', $id)->get();
    }
    public function getGalleryByPaginate($id, $value)
    {
        ManipulationActivity::noteManipulationAdmin("Xem Danh Sách Thư Viện Ảnh");
        return $this->model->where('room_id', $id)->paginate($value);
    }
    public function insert_item($id, $get_images)
    {
        /* Bên kia input name="file[]" nên gửi qua là 1 mảng chứa toàn bộ ảnh , sử dụng dd() để rõ hơn*/
        $room = $this->findRoom($id);
        $folder = preg_replace('/\s+/', '', $room->room_name); /* Hàm xóa sạch ký tư trắng */

        if ($get_images) {
            foreach ($get_images as $get_image) {
                $get_image_name = $get_image->getClientOriginalName(); /* Lấy Tên File */
                $image_name = current(explode('.', $get_image_name)); /* VD Tên File Là nhan.jpg thì hàm explode dựa vào dấm . để phân tách thành 2 chuổi là nhan và jpg , còn hàm current để chuổi đầu , hàm end thì lấy cuối */
                $new_image = $image_name . rand(0, 99) . '.' . $get_image->getClientOriginalExtension(); /* getClientOriginalExtension() hàm lấy phần mở rộng của ảnh */
                $get_image->move('public\fontend\assets\img\hotel\room\gallery_' . $folder, $new_image);

                $gallery = array(
                    'room_id' => $id,
                    'gallery_room_name' => $image_name,
                    'gallery_room_image' => $new_image,
                    'gallery_room_content' => "Chưa có nội dung !",
                );
                $this->create($gallery);
            }
        }
        ManipulationActivity::noteManipulationAdmin("Thêm Vào " . count($get_images) . " Hình Ảnh Vào Thư Viện ( ID : " . $id . ")");
        $this->message("success", "Thêm Vào " . count($get_images) . " Hình Ảnh Vào Thư Viện Thành Công !");
        return redirect()->back();
    }

    public function delete_item($id)
    {
        $gallery_room = $this->find($id);
        $room = $this->findRoom( $gallery_room->room_id);
        $folder = preg_replace('/\s+/', '', $room->room_name);
        ManipulationActivity::noteManipulationAdmin("Xóa Ảnh Ở Thư Viện ( ID Ảnh : " . $id . ")");
        $this->delete($id);
        unlink('public/fontend/assets/img/hotel/room/gallery_' . $folder . '/' . $gallery_room->gallery_room_image); /* Xóa ảnh ở trong thư mục */
        echo "true";
    }
    public function update_content($id, $content)
    {
        $data['gallery_room_content'] = $content;
        $this->update($id, $data);
        ManipulationActivity::noteManipulationAdmin("Cập Nhật Nội Dung Ảnh ( ID Ảnh : " . $id . ")");
    }
    public function update_name($id, $name)
    {
        $data['gallery_room_name'] = $name;
        $this->update($id, $data);
        ManipulationActivity::noteManipulationAdmin("Cập Nhật Tên Ảnh ( ID Ảnh : " . $id . ")");
    }
    public function update_image($id, $get_image)
    {
        if ($get_image) {
            $gallery_room = $this->find($id);
            $room = $this->findRoom( $gallery_room->room_id);
            $folder = preg_replace('/\s+/', '', $room->room_name);

            $get_image_name = $get_image->getClientOriginalName(); /* Lấy Tên File */
            $image_name = current(explode('.', $get_image_name)); /* VD Tên File Là nhan.jpg thì hàm explode dựa vào dấm . để phân tách thành 2 chuổi là nhan và jpg , còn hàm current để chuổi đầu , hàm end thì lấy cuối */
            $new_image = $image_name . rand(0, 99) . '.' . $get_image->getClientOriginalExtension(); /* getClientOriginalExtension() hàm lấy phần mở rộng của ảnh */
            $get_image->move('public\fontend\assets\img\hotel\room\gallery_' . $folder, $new_image);
            unlink('public/fontend/assets/img/hotel/room/gallery_'.$folder.'/'.$gallery_room->gallery_room_image); /* Xóa ảnh cũ trong thư mục */
            $data['gallery_room_image'] = $new_image;
            $this->update($id, $data);
            ManipulationActivity::noteManipulationAdmin("Cập Nhật Ảnh ( ID Ảnh : " . $id . ")");
        }

    }
    public function message($type, $content)
    {
        $message = array(
            "type" => "$type",
            "content" => "$content",
        );
        session()->flash('message', $message);
    }
    public function output_item($items, $id)
    {
        $room = $this->findRoom($id);
        $folder = preg_replace('/\s+/', '', $room->room_name); /* Hàm xóa sạch ký tư trắng */
        $output = '';
        $i = 0;
        $output.= '  <div style="display: flex;justify-content: space-between">
        <div class="card-title col-sm-9">Danh Sách Hình Ảnh '.$room->room_name.'
        </div>
        <div class="col-sm-3">
        </div>
    </div>
    <table style="margin-top:20px " class="table table-bordered">
    <thead>
        <tr>
            <th> #STT </th>
            <th>Tên Ảnh</th>
            <th>Hình Ảnh</th>
            <th>Nội Dung Ảnh</th>
            <th>Thao Tác</th>
        </tr>
    </thead>
    <tbody>';
        foreach ($items as $gallery) {
            $output .= '
          
            <tr>
                <td>  ' . ++$i . ' </td>
                <td contentEditable class="update_gallery_room_name"  data-gallery_room_id = "' . $gallery->gallery_room_id . '"> <div style="width: 100px;overflow: hidden;">  ' . $gallery->gallery_room_name . ' </div>  </td>
                <td>
                <input hidden id="up_load_file' . $gallery->gallery_room_id . '" class="up_load_file"  type="file" name="file_image" accept="image/*" data-gallery_room_id = "' . $gallery->gallery_room_id . '">
                <label class="up_load_file" for="up_load_file' . $gallery->gallery_room_id . '" >
               <img style="object-fit: cover" width="40px" height="20px" src=' . URL('public/fontend/assets/img/hotel/room/gallery_' . $folder . '/' . $gallery->gallery_room_image) . ' alt="' . $gallery->gallery_room_content . '">
                </label>
               </td>
                <td  contentEditable  class="edit_gallery_room_content"  data-gallery_room_id = "' . $gallery->gallery_room_id . '"><div style="width: 200px;overflow: hidden">  ' . $gallery->gallery_room_content . ' </div>  </td>
                <td>';
            // if(hasanyroles(["admin","manager"])){
            $output .= '
                    <button  style="border: none" class="delete_gallery_room" data-gallery_room_id = "' . $gallery->gallery_room_id . '"><i style="font-size: 22px" class="mdi mdi-delete-sweep text-danger "></i></button>
                    ';
            // }
            $output .= '
                </td>
            </tr>
          
            ';

        }
        $output .='  </tbody>
        </table>';
        return $output;
    }
}
