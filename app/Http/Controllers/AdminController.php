<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Roles;
use App\Rules\Captcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\AdminRepository\AdminRepositoryInterface;
use Illuminate\Support\Facades\Redirect;
use Session;

session_start();
class AdminController extends Controller
{
    /**
     * @var PostRepositoryInterface|\App\Repositories\Repository
     */
    protected $adminRepo;
    
    public function __construct(AdminRepositoryInterface $adminRepo)
    {
        $this->adminRepo = $adminRepo;
    }

    /* Admin Auth */
    public function all_admin()
    {
        $admins = $this->adminRepo->getAllByPaginate(3);
        return view('admin.Admin.all_admin')->with(compact('admins'));
    }

    public function assign_roles(Request $request)
    {
       $result = $this->adminRepo->assign_roles($request->all());
       echo  $result;
    }

    public function delete_admin_roles(Request $request)
    {
        $result = $this->adminRepo->delete_admin_roles($request->admin_id);
        echo $result;
    }
    public function impersonate(Request $request)
    {
        $admin = $this->adminRepo->find($request->admin_id);
        if ($admin) {
            session()->put('impersonate', $admin->admin_id);
        }
        $this->message("success", "Chuyển Quyền Thành Công !");
        return redirect('admin/dashboard');
    }
    public function destroy_impersonate()
    {
        session()->forget('impersonate');
        $this->message("success", "Hủy Chuyển Quyền Thành Công !");
        return redirect('admin/auth/all-admin');
    }

    public function edit_admin(Request $request){
        $admin = $this->adminRepo->find($request->admin_id);
        return view('admin.Admin.edit_admin')->with(compact('admin'));
    }

    public function update_admin(Request $request){
        $result = $this->adminRepo->update_admin($request->all());   
        if($result == "error"){
            $this->message('warning', 'Mật Khẩu Xác Nhận Không Giống Nhau');
            return Redirect()->back();
        }else if($result == "true"){
            $this->message('success', 'Đã cập nhật thành công');
            return Redirect('admin/auth/all-admin');
        }
    }

    public function search_all_admin(Request $request){
        $result =  $this->adminRepo->searchNameOrEmail($request->key_sreach);
        $output = $this->adminRepo->output_admin($result);
        echo $output;
    }

    public function loading_table_admin(){
        $result =  $this->adminRepo->getAllByPaginate(3);
        $output =$this->adminRepo->output_admin($result);
        echo $output;  
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
