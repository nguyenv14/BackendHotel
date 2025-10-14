<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ManipulationActivity;
use App\Repositories\ServiceChargeRepository\ServiceChargeRepositoryInterface;
use App\Models\Hotel;
use Auth;
session_start();

class ServiceChargeController extends Controller
{
    /**
     * @var PostRepositoryInterface|\App\Repositories\Repository
     */ 
    protected $servicechargeRepo;
    public function __construct(ServiceChargeRepositoryInterface $servicechargeRepo)
    {
        $this->servicechargeRepo = $servicechargeRepo;
    }
    public function insert_item(){
        $hotels = $this->servicechargeRepo->getAllHotel();
        return view('admin.servicecharge.add_service_charge')->with(compact('hotels'));
    }

    public function list_items(){
        $items = $this->servicechargeRepo->getAllByPaginate(5);
        return view('admin.servicecharge.all_service_charge')->with(compact('items'));
    }
    public function load_items(){
        $items = $this->servicechargeRepo->getAllByPaginate(5);
        $output =  $this->servicechargeRepo->output_item($items);
        echo $output;
    }

    public function save_item(Request $request){
        $Servicecharge =  $this->servicechargeRepo->getAll();
        foreach($Servicecharge as  $servicecharge){
            if($servicecharge->hotel_id == $request->hotel_id){
                $this->servicechargeRepo->message("warning",'Khách Sạn Này Đã Được Tính Phí Dịch Vụ!');
                return redirect('admin/servicecharge/all-service-charge');
            }
        }
        $result = $this->servicechargeRepo->insert_item($request->all());
        return redirect('admin/servicecharge/all-service-charge');
      
    }
    public function edit_item(Request $request){
        $servicecharge_old = $this->servicechargeRepo->find($request->servicecharge_id);
        return view('admin.servicecharge.edit_service_charge')->with(compact('servicecharge_old'));
    }

    public function update_item(Request $request){
        $result = $this->servicechargeRepo->update_item($request->all());
        return redirect('admin/servicecharge/all-service-charge');
    }
    public function update_status_item(Request $request){
        $result = $this->servicechargeRepo->update_status($request->all());
    }
    public function search_items(Request $request){
        $result =  $this->servicechargeRepo->searchIDorName($request->key_sreach);
        $output = $this->servicechargeRepo->output_item($result);
        echo $output;
    }
    public function move_to_bin(Request $request){
        $this->servicechargeRepo->move_bin($request->servicecharge_id);
        return redirect('admin/servicecharge/all-service-charge');
    }

    public function count_bin(){
        $result = $this->servicechargeRepo->count_bin();
        echo $result;
    }
    public function list_bin(){
        $items = $this->servicechargeRepo->getItemBinByPaginate(5);
        return view('admin.servicecharge.soft_deleted_service_charge')->with(compact('items'));
    }
    function load_bin(){
        $servicecharges =  $this->servicechargeRepo->getItemBinByPaginate(5);
        $output = $this->servicechargeRepo->output_item_bin($servicecharges);
        echo $output;
    }
    public function search_bin(Request $request){
        $output = $this->servicechargeRepo->search_bin($request->key_sreach);
        echo $output;
    }
    public function bin_delete(Request $request)
    {
        $result =  $this->servicechargeRepo->delete_item($request->servicecharge_id);
    }

    public function un_bin(Request $request)
    {
        $result =  $this->servicechargeRepo->restore_item($request->servicecharge_id);
    }
}
