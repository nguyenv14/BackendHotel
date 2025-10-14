<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\RoomRepository\RoomRepositoryInterface;

use Session;
session_start();

class RoomController extends Controller
{
     /**
     * @var PostRepositoryInterface|\App\Repositories\Repository
     */
    protected $roomRepo;
    public function __construct(RoomRepositoryInterface $roomRepo)
    {
        $this->roomRepo = $roomRepo;
    }
    public function list_items(Request $request){
        $items = $this->roomRepo->getAllByPaginate($request->hotel_id , 5);
        $hotel = $this->roomRepo->getHotel($request->hotel_id);
        return view('admin.Hotel.ManagerHotel.Room.all_room')->with(compact('hotel','items'));
     
    }
    public function load_items(Request $request){
        $items = $this->roomRepo->getAllByPaginate($request->hotel_id , 5);
        $output =  $this->roomRepo->output_item($items);
        echo $output;
    }
    public function insert_item(Request $request){
        $hotel = $this->roomRepo->getHotel($request->hotel_id);
        return view('admin.Hotel.ManagerHotel.Room.add_room')->with(compact('hotel'));
    }
    public function save_item(Request $request){
        $result = $this->roomRepo->insert_item($request->all());
        return redirect('admin/hotel/manager/room/all-room?hotel_id='.$request->hotel_id);
    }
    public function edit_item(Request $request){
        $room_old = $this->roomRepo->find($request->room_id);
        $hotel = $this->roomRepo->getHotel($request->hotel_id);
        return view('admin.Hotel.ManagerHotel.Room.edit_room')->with(compact('room_old','hotel'));
    }

    public function update_item(Request $request){
        $result = $this->roomRepo->update_item($request->all());
        return redirect('admin/hotel/manager/room/all-room?hotel_id='.$request->hotel_id);
    }
    public function update_status_item(Request $request){
        $result = $this->roomRepo->update_status($request->all());
    }
    public function search_items(Request $request){
        $result =  $this->roomRepo->searchIDorName($request->all());
        $output = $this->roomRepo->output_item($result);
        echo $output;
    }
    public function move_to_bin(Request $request){
        $this->roomRepo->move_bin($request->room_id);
    }

    public function count_bin(Request $request){
        $result = $this->roomRepo->count_bin($request->hotel_id);
        echo $result;
    }
    public function list_bin(Request $request){
        $hotel = $this->roomRepo->getHotel($request->hotel_id);
        $items = $this->roomRepo->getItemBinByPaginate($request->hotel_id,5);
      
        return view('admin.Hotel.ManagerHotel.Room.soft_deleted_room')->with(compact('items','hotel'));
    }
    function load_bin(Request $request){
        $rooms =  $this->roomRepo->getItemBinByPaginate($request->hotel_id,5);
        $output = $this->roomRepo->output_item_bin($rooms);
        echo $output;
    }
    public function search_bin(Request $request){
        $output = $this->roomRepo->search_bin($request->all());
        echo $output;
    }
    public function bin_delete(Request $request)
    {
        $result =  $this->roomRepo->delete_item($request->room_id);
    }

    public function un_bin(Request $request)
    {
        $result =  $this->roomRepo->restore_item($request->room_id);
    }
}