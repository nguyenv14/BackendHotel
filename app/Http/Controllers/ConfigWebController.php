<?php

namespace App\Http\Controllers;
use App\Repositories\ConfigWebRepository\ConfigWebRepositoryInterface;
use App\Models\ConfigWeb;
use App\Models\ManipulationActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Session;

session_start();

class ConfigWebController extends Controller
{
    /**
     * @var PostRepositoryInterface|\App\Repositories\Repository
     */
    protected $configRepo;

    public function __construct(ConfigWebRepositoryInterface $configRepo)
    {
        $this->configRepo = $configRepo;
    }

    public function show_config()
    {
        $image_logo =$this->configRepo->get_logo();
        return view('admin.ConfigWeb.configweb')->with('image_logo', $image_logo);
    }

    public function loading_config_slogan()
    {
        $config_slogan = $this->configRepo->get_slogan();
        $result = $this->configRepo->output_slogan($config_slogan);
        echo $result;
    }

    public function load_logo_config()
    {
        $config_logo = $this->configRepo->get_logo();
        $result = $this->configRepo->output_logo($config_logo);
        echo $result;
    }

    public function load_config_brand()
    {
        $config_brands = $this->configRepo->get_brand();
        $result = $this->configRepo->output_brand($config_brands);
        echo $result;
    }

    public function insert_config_image(Request $request)
    {
       if($request->config_type == 1){
            $image_logo =$this->configRepo->get_logo();
            if($image_logo){
                unlink('public/fontend/assets/img/config/'.$image_logo->config_image);
                $image_logo->delete();
            }
       }
        $result = $this->configRepo->insert_config($request->config_type,$request->file('file'));
        ManipulationActivity::noteManipulationAdmin("Thêm Vào " . count($request->file('file')) . " Hình Ảnh Vào Cấu hình website");
        $this->message("success", "Thêm Vào " . count($request->file('file')) . " Hình Ảnh Vào Thư Viện Thành Công !");
        return redirect()->back();
    }

    public function edit_config_title(Request $request)
    {
        $this->configRepo->update_config($request->all());
        ManipulationActivity::noteManipulationAdmin("Cập Nhật Tiêu Đề Ảnh Cấu Hình( ID Ảnh : " . $request->config_id . ")");
    }

    public function edit_config_content(Request $request)
    {
        $this->configRepo->update_config($request->all());
        ManipulationActivity::noteManipulationAdmin("Cập Nhật Tên Ảnh ( ID Ảnh : " . $request->config_id . ")");
    }

    public function delete_config(Request $request)
    {
        $this->configRepo->delete_config($request->config_id);
    }

    public function update_image_config(Request $request)
    {
        $this->configRepo->update_img($request->config_id, $request->file('file')); 
        ManipulationActivity::noteManipulationAdmin("Cập Nhật Ảnh ( ID Ảnh : " . $request->config_id . ")");
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
