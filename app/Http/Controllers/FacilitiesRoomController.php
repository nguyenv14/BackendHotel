<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\FacilitiesRoomRepository\FacilitiesRoomRepositoryInterface;

use Session;
session_start();

class FacilitiesRoomController extends Controller
{
     /**
     * @var PostRepositoryInterface|\App\Repositories\Repository
     */ 
    protected $faciroomRepo;
    public function __construct(FacilitiesRoomRepositoryInterface $faciroomRepo)
    {
        $this->faciroomRepo = $faciroomRepo;
    }
    public function list_items(){
        $items = $this->faciroomRepo->getAllByPaginate(5);
        return view('admin.Facilities.FacilitiesRoom.all_facilities_room')->with(compact('items'));
    }
    public function load_items(){
        $items = $this->faciroomRepo->getAllByPaginate(5);
        $output =  $this->faciroomRepo->output_item($items);
        echo $output;
    }
    public function insert_item(){
        return view('admin.Facilities.FacilitiesRoom.add_facilities_room');
    }
    public function save_item(Request $request){
        $result = $this->faciroomRepo->insert_item($request->all(),$request->file('facilitiesroom_image'));
        return redirect('admin/hotel/facilities/room/all-room-facilities');
    }
    public function edit_item(Request $request){
        $facilitiesroom_old = $this->faciroomRepo->find($request->facilitiesroom_id);
        return view('admin.Facilities.FacilitiesRoom.edit_facilities_room')->with(compact('facilitiesroom_old'));
    }

    public function update_item(Request $request){
        $result = $this->faciroomRepo->update_item($request->all(),$request->file('facilitiesroom_image'));
        return redirect('admin/hotel/facilities/room/all-room-facilities');
    }
    public function update_status_item(Request $request){
        $result = $this->faciroomRepo->update_status($request->all());
    }
    public function search_items(Request $request){
        $result =  $this->faciroomRepo->searchIDorName($request->key_sreach);
        $output = $this->faciroomRepo->output_item($result);
        echo $output;
    }
    public function move_to_bin(Request $request){
        $this->faciroomRepo->move_bin($request->facilitiesroom_id);
        return redirect('admin/hotel/facilities/room/all-room-facilities');
    }

    public function count_bin(){
        $result = $this->faciroomRepo->count_bin();
        echo $result;
    }
    public function list_bin(){
        $items = $this->faciroomRepo->getItemBinByPaginate(5);
        return view('admin.Facilities.FacilitiesRoom.soft_deleted_facilities_room')->with(compact('items'));
    }
    function load_bin(){
        $facilitiesrooms =  $this->faciroomRepo->getItemBinByPaginate(5);
        $output = $this->faciroomRepo->output_item_bin($facilitiesrooms);
        echo $output;
    }
    public function search_bin(Request $request){
        $output = $this->faciroomRepo->search_bin($request->key_sreach);
        echo $output;
    }
    public function bin_delete(Request $request)
    {
        $result =  $this->faciroomRepo->delete_item($request->facilitiesroom_id);
    }

    public function un_bin(Request $request)
    {
        $result =  $this->faciroomRepo->restore_item($request->facilitiesroom_id);
    }
}