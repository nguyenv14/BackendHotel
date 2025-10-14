<?php

namespace App\Http\Controllers;

use App\Models\ManipulationActivity;
use App\Repositories\ManipulationActivityRepository\ManipulationActivityRepositoryInterface;
use Session;

session_start();
class ManipulationActivityController extends Controller
{
    /**
     * @var PostRepositoryInterface|\App\Repositories\Repository
     */
    protected $maniRepo;

    public function __construct(ManipulationActivityRepositoryInterface $maniRepo)
    {
        $this->maniRepo = $maniRepo;
    }
    /* Nhật Ký Của Admin */
    public function all_manipulation_admin()
    {
        $admin_manipulation =  $this->maniRepo->getAllManiAdminByPaginate(10);
        ManipulationActivity::noteManipulationAdmin("Xem Bảng Nhật Ký Thao Tác Admin");
        return view('admin.Activity.ManipulationActivity.all_manipulation_admin')->with(compact('admin_manipulation'));
    }
    /* Nhật Ký Của Người Dùng */
    public function all_manipulation_customer()
    {
        $customer_manipulation =  $this->maniRepo->getAllManiCustomerByPaginate(10);
        ManipulationActivity::noteManipulationAdmin("Xem Bảng Nhật Ký Thao Tác Người Dùng");
        return view('admin.Activity.ManipulationActivity.all_manipulation_customer')->with(compact('customer_manipulation'));
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
