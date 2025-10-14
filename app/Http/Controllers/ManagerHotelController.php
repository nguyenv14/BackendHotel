<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\ManagerHotelRepository\ManagerHotelRepositoryInterface;

use Session;
session_start();

class ManagerHotelController extends Controller
{
     /**
     * @var PostRepositoryInterface|\App\Repositories\Repository
     */
    protected $hotelRepo;
    public function __construct(ManagerHotelRepositoryInterface $hotelRepo)
    {
        $this->hotelRepo = $hotelRepo;
    }
    public function list_items(){
        $items = $this->hotelRepo->getAllByPaginate(3);
        return view('admin.Hotel.all_hotel')->with(compact('items'));
    }
    public function load_items(){
        $items = $this->hotelRepo->getAllByPaginate(3);
        $output =  $this->hotelRepo->output_item($items);
        echo $output;
    }
    public function insert_item(){
        $areas = $this->hotelRepo->getArea();
        $brands = $this->hotelRepo->getBrand();
        return view('admin.Hotel.add_hotel')->with(compact('areas','brands'));
    }
    public function save_item(Request $request){
        $result = $this->hotelRepo->insert_item($request->all(),$request->file('hotel_image'));
        if ($result === false) {
            return redirect()->back()->withInput();
        }
        return redirect('/admin/hotel/all-hotel');
    }
    public function update_status_item(Request $request){
        $result = $this->hotelRepo->update_status($request->all());
    }
    public function search_items(Request $request){
        $result =  $this->hotelRepo->searchIDorName($request->key_sreach);
        $output = $this->hotelRepo->output_item($result);
        echo $output;
    }
    public function move_to_bin(Request $request){
        $this->hotelRepo->move_bin($request->hotel_id);
        return redirect('/admin/hotel/all-hotel');
    }

    public function count_bin(){
        $result = $this->hotelRepo->count_bin();
        echo $result;
    }
    public function list_bin(){
        $items = $this->hotelRepo->getItemBinByPaginate(3);
        return view('admin.Hotel.soft_deleted_hotel')->with(compact('items'));
    }
    function load_bin(){
        $hotels =  $this->hotelRepo->getItemBinByPaginate(3);
        $output = $this->hotelRepo->output_item_bin($hotels);
        echo $output;
    }
    public function search_bin(Request $request){
        $output = $this->hotelRepo->search_bin($request->key_sreach);
        echo $output;
    }
    public function bin_delete(Request $request)
    {
        $result =  $this->hotelRepo->delete_item($request->hotel_id);
    }

    public function un_bin(Request $request)
    {
        $result =  $this->hotelRepo->restore_item($request->hotel_id);
    }







    public function index(Request $request){
        $hotel = $this->hotelRepo->find($request->hotel_id);
        return view('admin.Hotel.ManagerHotel.index')->with(compact('hotel'));
    }
    public function edit_item(Request $request){
        $areas = $this->hotelRepo->getArea();
        $brands = $this->hotelRepo->getBrand();
        $hotel = $this->hotelRepo->find($request->hotel_id);
        return view('admin.Hotel.ManagerHotel.edit_hotel')->with(compact('hotel','areas','brands'));
    }

    public function update_item(Request $request){
        $result = $this->hotelRepo->update_item($request->all(),$request->file('hotel_image'));
        return redirect('/admin/hotel/all-hotel');
    }
    
}