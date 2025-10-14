<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\AreaRepository\AreaRepositoryInterface;

use Session;
session_start();

class AreaController extends Controller
{
     /**
     * @var PostRepositoryInterface|\App\Repositories\Repository
     */
    protected $areaRepo;
    public function __construct(AreaRepositoryInterface $areaRepo)
    {
        $this->areaRepo = $areaRepo;
    }
    public function list_items(){
        $items = $this->areaRepo->getAllByPaginate(3);
        return view('admin.Area.all_area')->with(compact('items'));
    }
    public function load_items(){
        $items = $this->areaRepo->getAllByPaginate(3);
        $output =  $this->areaRepo->output_item($items);
        echo $output;
    }
    public function insert_item(){
        return view('admin.Area.add_area');
    }
    public function save_item(Request $request){
        $result = $this->areaRepo->insert_item($request->all(),$request->file('area_image'));
        return redirect('/admin/area/all-area');
    }
    public function edit_item(Request $request){
        $area_old = $this->areaRepo->find($request->area_id);
        return view('admin.Area.edit_area')->with(compact('area_old'));
    }

    public function update_item(Request $request){
        $result = $this->areaRepo->update_item($request->all(),$request->file('area_image'));
        return redirect('/admin/area/all-area');
    }
    public function update_status_item(Request $request){
        $result = $this->areaRepo->update_status($request->all());
    }
    public function search_items(Request $request){
        $result =  $this->areaRepo->searchIDorName($request->key_sreach);
        $output = $this->areaRepo->output_item($result);
        echo $output;
    }
    public function move_to_bin(Request $request){
        $this->areaRepo->move_bin($request->area_id);
        return redirect('/admin/area/all-area');
    }

    public function count_bin(){
        $result = $this->areaRepo->count_bin();
        echo $result;
    }
    public function list_bin(){
        $items = $this->areaRepo->getItemBinByPaginate(3);
        return view('admin.Area.soft_deleted_area')->with(compact('items'));
    }
    function load_bin(){
        $areas =  $this->areaRepo->getItemBinByPaginate(3);
        $output = $this->areaRepo->output_item_bin($areas);
        echo $output;
    }
    public function search_bin(Request $request){
        $output = $this->areaRepo->search_bin($request->key_sreach);
        echo $output;
    }
    public function bin_delete(Request $request)
    {
        $result =  $this->areaRepo->delete_item($request->area_id);
    }

    public function un_bin(Request $request)
    {
        $result =  $this->areaRepo->restore_item($request->area_id);
    }
}