<?php

namespace App\Http\Controllers;
use App\Repositories\ActivityLogRepository\ActivityLogRepositoryInterface;
use App\Models\Activitylog;
use App\Models\ManipulationActivity;
use Session;

session_start();
class ActivityLogController extends Controller
{
    /**
     * @var PostRepositoryInterface|\App\Repositories\Repository
     */
    protected $actiRepo;

    public function __construct(ActivityLogRepositoryInterface $actiRepo)
    {
        $this->actiRepo = $actiRepo;
    }
    /* Nhật Ký Đăng Nhập - Đăng Xuất Của Admin */
    public function all_activity_admin()
    {
        $admin_activities =  $this->actiRepo->getAllAdminByPaginate(10);
        ManipulationActivity::noteManipulationAdmin("Xem Bảng Hoạt Động Đăng Nhập - Đăng Xuất Admin");
        return view('admin.Activity.Activitylog.all_activity_admin')->with(compact('admin_activities'));
    }
    /* Nhật Ký Của Người Dùng */
    public function all_activity_customer()
    {
        $customer_activities = $this->actiRepo->getAllCustomerByPaginate(10);
        ManipulationActivity::noteManipulationAdmin("Xem Bảng Hoạt Động Đăng Nhập - Đăng Xuất Người Dùng");
        return view('admin.Activity.Activitylog.all_activity_customer')->with(compact('customer_activities'));
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
