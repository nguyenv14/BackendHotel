<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\TypeRoomRepository\TypeRoomRepositoryInterface;

use Session;
session_start();

class TypeRoomController extends Controller
{
     /**
     * @var PostRepositoryInterface|\App\Repositories\Repository
     */
    protected $typeroomRepo;
    public function __construct(TypeRoomRepositoryInterface $typeroomRepo)
    {
        $this->typeroomRepo = $typeroomRepo;
    }
    public function insert_item(Request $request){
        $hotel = $this->typeroomRepo->getHotel($request->hotel_id);
        $rooms = $this->typeroomRepo->getAllRoomByHotel($request->hotel_id);
        return view('admin.Hotel.ManagerHotel.Room.TypeRoom.add_type_room')->with(compact('hotel','rooms'));
    }
    public function list_items(Request $request){
        $items = $this->typeroomRepo->getAllByPaginate($request->room_id,5);
        $hotel = $this->typeroomRepo->getHotel($request->hotel_id);
        return view('admin.Hotel.ManagerHotel.Room.TypeRoom.all_type_room')->with(compact('hotel','items'));
    }
    public function load_items(Request $request){
        $items = $this->typeroomRepo->getAllByPaginate($request->room_id,5);
        $output =  $this->typeroomRepo->output_item($items);
        echo $output;
    }
    public function save_item(Request $request){
        $result = $this->typeroomRepo->insert_item($request->all());
        return redirect('admin/hotel/manager/room/typeroom/all-typeroom?hotel_id='.$request->hotel_id.'&room_id='.$request->room_id);
      
    }
    public function edit_item(Request $request){
        $typeroom_old = $this->typeroomRepo->find($request->type_room_id);
        $hotel = $this->typeroomRepo->getHotel($request->hotel_id);
        return view('admin.Hotel.ManagerHotel.Room.TypeRoom.edit_type_room')->with(compact('typeroom_old','hotel'));
    }

    public function update_item(Request $request){
        $result = $this->typeroomRepo->update_item($request->all());
        return redirect('admin/hotel/manager/room/typeroom/all-typeroom?hotel_id='.$request->hotel_id.'&room_id='.$request->room_id);
    }
    public function update_status_item(Request $request){
        $result = $this->typeroomRepo->update_status($request->all());
    }
    public function search_items(Request $request){
        $result =  $this->typeroomRepo->searchIDorName($request->all());
        $output = $this->typeroomRepo->output_item($result);
        echo $output;
    }

    public function move_to_bin(Request $request){
        $this->typeroomRepo->move_bin($request->type_room_id);
    }

    public function count_bin(Request $request){
        $result = $this->typeroomRepo->count_bin($request->room_id);
        echo $result;
    }
    public function list_bin(Request $request){
        $items = $this->typeroomRepo->getAllByPaginate($request->room_id,5);
        $hotel = $this->typeroomRepo->getHotel($request->hotel_id);
        return view('admin.Hotel.ManagerHotel.Room.TypeRoom.soft_deleted_type_room')->with(compact('items','hotel'));
    }
    function load_bin(Request $request){
        $typerooms =  $this->typeroomRepo->getItemBinByPaginate($request->room_id,5);
        $output = $this->typeroomRepo->output_item_bin($typerooms);
        echo $output;
    }
    public function search_bin(Request $request){
        $output = $this->typeroomRepo->search_bin($request->all());
        echo $output;
    }
    public function bin_delete(Request $request)
    {
        $result =  $this->typeroomRepo->delete_item($request->type_room_id);
    }

    public function un_bin(Request $request)
    {
        $result =  $this->typeroomRepo->restore_item($request->type_room_id);
    }
}