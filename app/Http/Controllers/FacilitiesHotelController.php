<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\FacilitiesHotelRepository\FacilitiesHotelRepositoryInterface;

use Session;
session_start();

class FacilitiesHotelController extends Controller
{
     /**
     * @var PostRepositoryInterface|\App\Repositories\Repository
     */ 
    protected $facihotelRepo;
    public function __construct(FacilitiesHotelRepositoryInterface $facihotelRepo)
    {
        $this->facihotelRepo = $facihotelRepo;
    }
    public function list_items(){
        $items = $this->facihotelRepo->getAllByPaginate(5);
        return view('admin.Facilities.FacilitiesHotel.all_facilities_hotel')->with(compact('items'));
    }
    public function load_items(){
        $items = $this->facihotelRepo->getAllByPaginate(5);
        $output =  $this->facihotelRepo->output_item($items);
        echo $output;
    }
    public function insert_item(){
        return view('admin.Facilities.FacilitiesHotel.add_facilities_hotel');
    }
    public function save_item(Request $request){
        $result = $this->facihotelRepo->insert_item($request->all(),$request->file('facilitieshotel_image'));
        return redirect('admin/hotel/facilities/hotel/all-hotel-facilities');
    }
    public function edit_item(Request $request){
        $facilitieshotel_old = $this->facihotelRepo->find($request->facilitieshotel_id);
        return view('admin.Facilities.FacilitiesHotel.edit_facilities_hotel')->with(compact('facilitieshotel_old'));
    }

    public function update_item(Request $request){
        $result = $this->facihotelRepo->update_item($request->all(),$request->file('facilitieshotel_image'));
        return redirect('admin/hotel/facilities/hotel/all-hotel-facilities');
    }
    public function update_status_item(Request $request){
        $result = $this->facihotelRepo->update_status($request->all());
    }
    public function search_items(Request $request){
        $result =  $this->facihotelRepo->searchIDorName($request->key_sreach);
        $output = $this->facihotelRepo->output_item($result);
        echo $output;
    }
    public function move_to_bin(Request $request){
        $this->facihotelRepo->move_bin($request->facilitieshotel_id);
        return redirect('admin/hotel/facilities/hotel/all-hotel-facilities');
    }

    public function count_bin(){
        $result = $this->facihotelRepo->count_bin();
        echo $result;
    }
    public function list_bin(){
        $items = $this->facihotelRepo->getItemBinByPaginate(3);
        return view('admin.Facilities.FacilitiesHotel.soft_deleted_facilities_hotel')->with(compact('items'));
    }
    function load_bin(){
        $facilitieshotels =  $this->facihotelRepo->getItemBinByPaginate(3);
        $output = $this->facihotelRepo->output_item_bin($facilitieshotels);
        echo $output;
    }
    public function search_bin(Request $request){
        $output = $this->facihotelRepo->search_bin($request->key_sreach);
        echo $output;
    }
    public function bin_delete(Request $request)
    {
        $result =  $this->facihotelRepo->delete_item($request->facilitieshotel_id);
    }

    public function un_bin(Request $request)
    {
        $result =  $this->facihotelRepo->restore_item($request->facilitieshotel_id);
    }
}