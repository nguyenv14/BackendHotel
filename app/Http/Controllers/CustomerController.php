<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Social;
use Illuminate\Http\Request;
use Mail;
use Auth;
use Session;
use App\Models\ManipulationActivity;
use App\Repositories\CustomerRepository\CustomerRepositoryInterface;
session_start();
class CustomerController extends Controller
{
    /**
     * @var PostRepositoryInterface|\App\Repositories\Repository
     */
    protected $customerRepo;
    
    public function __construct(CustomerRepositoryInterface $customerRepo)
    {
        $this->customerRepo = $customerRepo;
    }

    public function count_bin(){
        $result = $this->customerRepo->count_bin();
        echo $result;
    }
    public function all_customer()
    {
        $customers =  $this->customerRepo->getAllByPaginate(5);
        return view('admin.customer.all_customer')->with(compact('customers'));
    }

    public function load_all_customer()
    {
        $result = $this->customerRepo->getAllByPaginate(5);
        $output = $this->customerRepo->output_customer($result);
        echo $output ;
    }
    public function search_all_customer(Request $request){
        $result =  $this->customerRepo->searchNameOrEmail($request->key_sreach);
        $output = $this->customerRepo->output_customer($result);
        echo $output;
    }
    public function sort_customer_bytype(Request $request){
        $type =  $request->type;
        $customers = $this->customerRepo->getAll();
        if($type == 0){
            $output = $this->customerRepo->output_customer($customers);
            echo $output;
        }else if($type == 1){
            $output =  $this->customerRepo->output_customer_system($customers);
            echo  $output;
        }else if($type == 2){
            $output =  $this->customerRepo->output_sort_type($customers,'facebook');
            echo  $output;
        }else if($type == 3){
            $output =  $this->customerRepo->output_sort_type($customers,'google');
            echo  $output;
        }
      
    }

    public function update_status_customer(Request $request){
        $result = $this->customerRepo->update_status($request->all());
        if($result){
            if($request->status == 1){
                ManipulationActivity::noteManipulationAdmin( "Mở Khóa Tài Khoản ( ID : ".$request->customer_id.")");
            }else if($request->status == 0){
                ManipulationActivity::noteManipulationAdmin( "Vô Hiệu Tài Khoản ( ID : ".$request->customer_id.")");
            }
        }
    }
    public function delete_customer(Request $request){
       $result =  $this->customerRepo->delete_customer($request->customer_id);
       if($result){
        ManipulationActivity::noteManipulationAdmin( "Xóa Tài Khoản Vào Thùng Rác( ID : ".$request->customer_id.")");
       }
    }
    public function garbage_can(){
        $customers = $this->customerRepo->binByPaginate(5);
        ManipulationActivity::noteManipulationAdmin( "Xem Tài Khoản Khách Hàng Trong Thùng Rác");
        return view('admin.Customer.soft_deleted_customer')->with(compact('customers'));
    }

    function load_garbage_can(){
        $customers =  $this->customerRepo->binByPaginate(5);
        $output = $this->customerRepo->output_garbage_can($customers);
        echo $output;
    }
    public function search_bin(Request $request){
        $output = $this->customerRepo->output_search_bin($request->key_sreach);
        echo $output;
    }
    public function trash_delete(Request $request)
    {
        $result =  $this->customerRepo->delete_item($request->customer_id);
        ManipulationActivity::noteManipulationAdmin( "Xóa Vĩnh Viển Tài Khoản ( ID : ". $request->customer_id.")");
    }

    public function un_trash(Request $request)
    {
        $result =  $this->customerRepo->restore_item($request->customer_id);
        ManipulationActivity::noteManipulationAdmin( "Khôi Phục Tài Khoản ( ID : ". $request->customer_id.")");
    }

    public function view_email()
    {
        $customers =$this->customerRepo->getAll();
        return view('admin.Customer.emailcustomer')->with(compact('customers'));
    }
    public function selected_email(Request $request)
    {
        $result = $this->customerRepo->selected_email($request->all());
        echo $result;
    }

    public function send_email(Request $request)
    {
        $to_name = "MyHotel";
        $to_email = $request->to_email;
        $title_email = $request->title_email;
        $content_email = $request->content_email;

        $result = $this->customerRepo->send_mail($to_name,$to_email,$title_email,$content_email);
        if($result){
            echo "true";
        }
    }
    public function load_list_mail()
    {
        $list_mail_customer = session()->get('list_mail_customer');
        $output = '';
        if ($list_mail_customer) {
            foreach ($list_mail_customer as $email) {
                $output .= $email . ',';
            }
        }
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
