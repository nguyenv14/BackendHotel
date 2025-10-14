<?php
namespace App\Repositories\ConfigWebRepository;

use App\Repositories\BaseRepository;

class ConfigWebRepository extends BaseRepository implements ConfigWebRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\ConfigWeb::class;
    }
    public function get_logo()
    {
        return $this->model::where('config_type', 1)->first();
    }
    public function get_slogan()
    {
        return $this->model::where('config_type', 2)->get();
    }
    public function get_brand()
    {
        return $this->model::where('config_type', 3)->get();
    }
    public function output_slogan($config_slogan)
    {
        $output = '';
        $i = 0;
        foreach ($config_slogan as $config_web) {
            $output .= '
            <tr>
                <td>  ' . ++$i . ' </td>
                <td>
                <form>
                ' . csrf_field() . '
                <input hidden id="up_load_file' . $config_web->config_id . '" class="up_load_file"  type="file" name="file_image" accept="image/*" data-config_id = "' . $config_web->config_id . '">
                <label class="up_load_file" for="up_load_file' . $config_web->config_id . '" > <img style="object-fit: cover" width="40px" height="20px"
                src="' . URL('public/fontend/assets/img/config/' . $config_web->config_image) . '" alt=""></label>
                </form>
               </td>
                <td contentEditable class="edit_config_title"  data-config_id = "' . $config_web->config_id . '"> <div style="width: 100px;overflow: hidden;">  ' . $config_web->config_title . ' </div>  </td>
                <td  contentEditable  class="edit_config_content"  data-config_id = "' . $config_web->config_id . '"><div style="width: 200px;overflow: hidden">  ' . $config_web->config_content . ' </div>  </td>
                <td>
                <button  style="border: none" class="delete_config_image" data-config_id = "' . $config_web->config_id . '"><i style="font-size: 22px" class="mdi mdi-delete-sweep text-danger "></i></button>
                </td>
            </tr>
            ';
        }
        return $output;
    }
    public function output_logo($config_logo)
    {
        $output = '';
        $output .= 
        '<form>' . csrf_field() . '
        <input hidden id="up_load_file1" class="up_load_file"  type="file" name="file_image" accept="image/*" data-config_id = "1">
        <label class="up_load_file" for="up_load_file1" > <img style="border-radius: 50%;object-fit: cover;width: 100px;height:auto;"
        src="' . URL('public/fontend/assets/img/config/' . $config_logo->config_image) . '" alt=""></label>
        </form>';
        return $output;
    }
    public function output_brand($config_brands)
    {
        $output = '';
        $i = 0;
        foreach ($config_brands as $config_brand) {
            $output .= '
            <tr>
                <td>  ' . ++$i . ' </td>
                <td>
                <form>
                ' . csrf_field() . '
                <input hidden id="up_load_file' . $config_brand->config_id . '" class="up_load_file"  type="file" name="file_image" accept="image/*" data-config_id = "' . $config_brand->config_id . '">
                <label class="up_load_file" for="up_load_file' . $config_brand->config_id . '" > <img style="object-fit: cover" width="40px" height="20px"
                src="' . URL('public/fontend/assets/img/config/' . $config_brand->config_image) . '" alt=""></label>
                </form>
               </td>
                <td contentEditable class="edit_config_title"  data-config_id = "' . $config_brand->config_id . '"> <div style="width: 100px;overflow: hidden;">  ' . $config_brand->config_title . ' </div>  </td>
                <td contentEditable class="edit_config_content" data-config_id = "' . $config_brand->config_id . '"> <div style="width: 250px;overflow: hidden;">  ' . $config_brand->config_content . ' </div> </td>
                <td>
                <button  style="border: none" class="delete_config_image" data-config_id = "' . $config_brand->config_id . '"><i style="font-size: 22px" class="mdi mdi-delete-sweep text-danger "></i></button>
                </td>
            </tr>
            ';
        }
        return $output;
    }

    public function insert_config($config_type,$get_images){
        if ($get_images) {
            foreach ($get_images as $get_image) {
                $get_image_name = $get_image->getClientOriginalName(); /* Lấy Tên File */
                $image_name = current(explode('.', $get_image_name)); /* VD Tên File Là nhan.jpg thì hàm explode dựa vào dấm . để phân tách thành 2 chuổi là nhan và jpg , còn hàm current để chuổi đầu , hàm end thì lấy cuối */
                $new_image = $image_name . rand(0, 99) . '.' . $get_image->getClientOriginalExtension(); /* getClientOriginalExtension() hàm lấy phần mở rộng của ảnh */
                $get_image->move('public\fontend\assets\img\config', $new_image);

                $config_web['config_title'] = "Ảnh này chưa có tiêu đề";
                $config_web['config_image'] = $new_image;
                $config_web['config_content'] = "Ảnh này chưa có nội dung !";
                $config_web['config_type'] = $config_type;
                
                $this->create($config_web);
            }
        }
    }
    public function update_config($data){
        $this->update($data['config_id'],$data);
    }
    public function delete_config($id){
        $result = $this->find($id);
        unlink('public/fontend/assets/img/config/'.$result->config_image);
        $this->delete($id);
    }
    public function update_img($config_id,$get_image){
        if ($get_image) {
            $get_image_name = $get_image->getClientOriginalName(); /* Lấy Tên File */
            $image_name = current(explode('.', $get_image_name)); /* VD Tên File Là nhan.jpg thì hàm explode dựa vào dấm . để phân tách thành 2 chuổi là nhan và jpg , còn hàm current để chuổi đầu , hàm end thì lấy cuối */
            $new_image = $image_name . rand(0, 99) . '.' . $get_image->getClientOriginalExtension(); /* getClientOriginalExtension() hàm lấy phần mở rộng của ảnh */
            $get_image->move('public/fontend/assets/img/config', $new_image);
            $config = $this->find($config_id);
            unlink('public/fontend/assets/img/config/'.$config->config_image); /* Xóa ảnh ở trong thư mục */
            $config['config_image'] = $new_image;
            $config->save();
        }
    }
}
