<?php
namespace App\Repositories\GalleryHotelRepository;

use App\Models\Hotel;
use Illuminate\Support\Facades\Redirect;
use App\Models\ManipulationActivity;
use App\Repositories\BaseRepository;
use Auth;

class GalleryHotelRepository extends BaseRepository implements GalleryHotelRepositoryInterface
{
    //lấy model tương ứng
    public function getModel()
    {
        return \App\Models\GalleryHotel::class;
    }
    public function findHotel($id){
        return Hotel::find($id);
    }
    public function getGalleryByPaginate($id, $type , $value)
    {
        ManipulationActivity::noteManipulationAdmin("Xem Danh Sách Thư Viện Ảnh");
        return $this->model->where('hotel_id', $id)->where('gallery_hotel_type',$type)->paginate($value);
    }
    public function insert_item($id, $get_images, $type)
    {
        /* Bên kia input name="file[]" nên gửi qua là 1 mảng chứa toàn bộ ảnh , sử dụng dd() để rõ hơn*/
        $hotel = $this->findHotel($id);
        $folder = preg_replace('/\s+/', '', $hotel->hotel_name); /* Hàm xóa sạch ký tư trắng */
        
        if ($get_images) {
            foreach ($get_images as $get_image) {
                $get_image_name = $get_image->getClientOriginalName(); /* Lấy Tên File */
                $image_name = current(explode('.', $get_image_name)); /* VD Tên File Là nhan.jpg thì hàm explode dựa vào dấm . để phân tách thành 2 chuổi là nhan và jpg , còn hàm current để chuổi đầu , hàm end thì lấy cuối */
                $new_image = $image_name . rand(0, 99) . '.' . $get_image->getClientOriginalExtension(); /* getClientOriginalExtension() hàm lấy phần mở rộng của ảnh */
                $get_image->move('public\fontend\assets\img\hotel\gallery_' . $folder, $new_image);
              
                $gallery = array(
                    'hotel_id' => $id,
                    'gallery_hotel_name' => $image_name,
                    'gallery_hotel_type' => $type,
                    'gallery_hotel_image' => $new_image,
                    'gallery_hotel_content' => "Chưa có nội dung !",
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
        $image = $this->find($id);
        $hotel_name = $image->hotel->hotel_name;
        $folder = preg_replace('/\s+/', '', $hotel_name);
        ManipulationActivity::noteManipulationAdmin( "Xóa Ảnh Ở Thư Viện ( ID Ảnh : ".$id.")");
        $this->delete($id);
        unlink('public/fontend/assets/img/hotel/gallery_'.$folder.'/'.$image->gallery_hotel_image); /* Xóa ảnh ở trong thư mục */
        echo "true";
    }
    public function update_content($id,$content)
    {
        $data['gallery_hotel_content'] = $content;
        $this->update($id,$data);
        ManipulationActivity::noteManipulationAdmin( "Cập Nhật Nội Dung Ảnh ( ID Ảnh : ".$id.")");
    }
    public function update_name($id,$name){
        $data['gallery_hotel_name'] = $name;
        $this->update($id,$data);
        ManipulationActivity::noteManipulationAdmin( "Cập Nhật Tên Ảnh ( ID Ảnh : ".$id.")");
    }
    public function update_image($id,$get_image){
        if ($get_image) {
            $image = $this->model->where('gallery_hotel_id',$id)->first();
            $hotel_name = $image->hotel->hotel_name;
            $folder = preg_replace('/\s+/', '', $hotel_name);
            $get_image_name = $get_image->getClientOriginalName(); /* Lấy Tên File */
            $image_name = current(explode('.', $get_image_name)); /* VD Tên File Là nhan.jpg thì hàm explode dựa vào dấm . để phân tách thành 2 chuổi là nhan và jpg , còn hàm current để chuổi đầu , hàm end thì lấy cuối */
            $new_image = $image_name . rand(0, 99) . '.' . $get_image->getClientOriginalExtension(); /* getClientOriginalExtension() hàm lấy phần mở rộng của ảnh */
            $get_image->move('public\fontend\assets\img\hotel\gallery_' . $folder, $new_image);
            // Fix Hàm Này Nha unlink('public/fontend/assets/img/hotel/gallery_'.$folder.'/'.$image->gallery_hotel_image); /* Xóa ảnh cũ trong thư mục */
            $data['gallery_hotel_image'] = $new_image;
            $this->update($id,$data);
            ManipulationActivity::noteManipulationAdmin( "Cập Nhật Ảnh ( ID Ảnh : ".$id.")");
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
    public function output_item($items,$id,$type)
    {
        $hotel = $this->findHotel($id);
        $folder = preg_replace('/\s+/', '', $hotel->hotel_name);
        $output = '';
        $i = 0;
        foreach ($items as $gallery) {
            $output .= '
            <tr>
                <td>  ' . ++$i . ' </td>
                <td contentEditable class="update_gallery_hotel_name"  data-gallery_hotel_id = "' . $gallery->gallery_hotel_id . '"> <div style="width: 100px;overflow: hidden;">  '. $gallery->gallery_hotel_name.' </div>  </td>
                <td>
                <input hidden id="up_load_file' . $gallery->gallery_hotel_id . '" class="up_load_file"  type="file" name="file_image" accept="image/*" data-gallery_hotel_id = "' . $gallery->gallery_hotel_id . '">
                <label class="up_load_file" for="up_load_file' . $gallery->gallery_hotel_id . '" > ';
                if($type == 1){
                    $output.= ' <img style="object-fit: cover" width="40px" height="20px" src=' . URL('public/fontend/assets/img/hotel/gallery_'.$folder.'/'.$gallery->gallery_hotel_image) .' alt="'.$gallery->gallery_hotel_content.'">';
                }else if($type == 2){
                    $output.= ' <video style="object-fit: cover" width="40px" height="20px" src=' . URL('public/fontend/assets/img/hotel/gallery_'.$folder.'/'.$gallery->gallery_hotel_image) .' alt="'.$gallery->gallery_hotel_content.'">';
                }               
                $output.= '
                </label>
               </td>
                <td  contentEditable  class="edit_gallery_hotel_content"  data-gallery_hotel_id = "' . $gallery->gallery_hotel_id . '"><div style="width: 200px;overflow: hidden">  '. $gallery->gallery_hotel_content .' </div>  </td>
                <td>';
                // if(hasanyroles(["admin","manager"])){
                    $output .= '
                    <button  style="border: none" class="delete_gallery_hotel" data-gallery_hotel_id = "' . $gallery->gallery_hotel_id . '"><i style="font-size: 22px" class="mdi mdi-delete-sweep text-danger "></i></button>
                    ';
                // }
                $output .= '
                </td>
            </tr>
            ';
        }
        return $output;
    }
}
