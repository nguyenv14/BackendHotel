<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\BannerADSRepository\BannerADSRepositoryInterface;

use Session;
session_start();

class BannerADSController extends Controller
{
     /**
     * @var PostRepositoryInterface|\App\Repositories\Repository
     */ 
    protected $bannerRepo;
    public function __construct(BannerADSRepositoryInterface $bannerRepo)
    {
        $this->bannerRepo = $bannerRepo;
    }
    public function list_items(){
        $items = $this->bannerRepo->getAllByPaginate(5);
        return view('admin.BannerADS.all_banner_ads')->with(compact('items'));
    }
    public function load_items(){
        $items = $this->bannerRepo->getAllByPaginate(5);
        $output =  $this->bannerRepo->output_item($items);
        echo $output;
    }
    public function insert_item(){
        return view('admin.BannerADS.add_banner_ads');
    }
    public function save_item(Request $request){
        $result = $this->bannerRepo->insert_item($request->all(),$request->file('bannerads_image'));
        return redirect('admin/bannerads/all-banner-ads');
    }
    public function edit_item(Request $request){
        $bannerads_old = $this->bannerRepo->find($request->bannerads_id);
        return view('admin.BannerADS.edit_banner_ads')->with(compact('bannerads_old'));
    }

    public function update_item(Request $request){
        $result = $this->bannerRepo->update_item($request->all(),$request->file('bannerads_image'));
        return redirect('admin/bannerads/all-banner-ads');
    }
    public function update_status_item(Request $request){
        $result = $this->bannerRepo->update_status($request->all());
    }
    public function search_items(Request $request){
        $result =  $this->bannerRepo->searchIDorName($request->key_sreach);
        $output = $this->bannerRepo->output_item($result);
        echo $output;
    }
    public function move_to_bin(Request $request){
        $this->bannerRepo->move_bin($request->bannerads_id);
        return redirect('admin/bannerads/all-banner-ads');
    }

    public function count_bin(){
        $result = $this->bannerRepo->count_bin();
        echo $result;
    }
    public function list_bin(){
        $items = $this->bannerRepo->getItemBinByPaginate(5);
        return view('admin.BannerADS.soft_deleted_banner_ads')->with(compact('items'));
    }
    function load_bin(){
        $bannerads =  $this->bannerRepo->getItemBinByPaginate(5);
        $output = $this->bannerRepo->output_item_bin($bannerads);
        echo $output;
    }
    public function search_bin(Request $request){
        $output = $this->bannerRepo->search_bin($request->key_sreach);
        echo $output;
    }
    public function bin_delete(Request $request)
    {
        $result =  $this->bannerRepo->delete_item($request->bannerads_id);
    }

    public function un_bin(Request $request)
    {
        $result =  $this->bannerRepo->restore_item($request->bannerads_id);
    }
}