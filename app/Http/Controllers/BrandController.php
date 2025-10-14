<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\BrandRepository\BrandRepositoryInterface;

use Session;
session_start();

class BrandController extends Controller
{
     /**
     * @var PostRepositoryInterface|\App\Repositories\Repository
     */
    protected $brandRepo;
    public function __construct(BrandRepositoryInterface $brandRepo)
    {
        $this->brandRepo = $brandRepo;
    }
    public function list_items(){
        $items = $this->brandRepo->getAllByPaginate(3);
        return view('admin.Brand.all_brand')->with(compact('items'));
    }
    public function load_items(){
        $items = $this->brandRepo->getAllByPaginate(3);
        $output =  $this->brandRepo->output_item($items);
        echo $output;
    }
    public function insert_item(){
        return view('admin.Brand.add_brand');
    }
    public function save_item(Request $request){
        $result = $this->brandRepo->insert_item($request->all());
        return redirect('/admin/brand/all-brand');
    }
    public function edit_item(Request $request){
        $brand_old = $this->brandRepo->find($request->brand_id);
        return view('admin.Brand.edit_brand')->with(compact('brand_old'));
    }

    public function update_item(Request $request){
        $result = $this->brandRepo->update_item($request->all());
        return redirect('/admin/brand/all-brand');
    }
    public function update_status_item(Request $request){
        $result = $this->brandRepo->update_status($request->all());
    }
    public function search_items(Request $request){
        $result =  $this->brandRepo->searchIDorName($request->key_sreach);
        $output = $this->brandRepo->output_item($result);
        echo $output;
    }
    public function move_to_bin(Request $request){
        $this->brandRepo->move_bin($request->brand_id);
        return redirect('/admin/brand/all-brand');
    }

    public function count_bin(){
        $result = $this->brandRepo->count_bin();
        echo $result;
    }
    public function list_bin(){
        $items = $this->brandRepo->getItemBinByPaginate(3);
        return view('admin.Brand.soft_deleted_brand')->with(compact('items'));
    }
    function load_bin(){
        $brands =  $this->brandRepo->getItemBinByPaginate(3);
        $output = $this->brandRepo->output_item_bin($brands);
        echo $output;
    }
    public function search_bin(Request $request){
        $output = $this->brandRepo->search_bin($request->key_sreach);
        echo $output;
    }
    public function bin_delete(Request $request)
    {
        $result =  $this->brandRepo->delete_item($request->brand_id);
    }

    public function un_bin(Request $request)
    {
        $result =  $this->brandRepo->restore_item($request->brand_id);
    }
}