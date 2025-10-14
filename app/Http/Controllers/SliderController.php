<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\SliderRepository\SliderRepositoryInterface;

use Session;
session_start();

class SliderController extends Controller
{
     /**
     * @var PostRepositoryInterface|\App\Repositories\Repository
     */
    protected $sliderRepo;
    public function __construct(SliderRepositoryInterface $sliderRepo)
    {
        $this->sliderRepo = $sliderRepo;
    }
    public function list_items(){
        $items = $this->sliderRepo->getAllByPaginate(3);
        return view('admin.Slider.all_slider')->with(compact('items'));
    }
    public function load_items(){
        $items = $this->sliderRepo->getAllByPaginate(3);
        $output =  $this->sliderRepo->output_item($items);
        echo $output;
    }
    public function insert_item(){
        return view('admin.Slider.add_slider');
    }
    public function save_item(Request $request){
        $result = $this->sliderRepo->insert_item($request->all(),$request->file('slider_image'));
        return redirect('/admin/slider/all-slider');
    }
    public function edit_item(Request $request){
        $slider_old = $this->sliderRepo->find($request->slider_id);
        return view('admin.Slider.edit_slider')->with(compact('slider_old'));
    }

    public function update_item(Request $request){
        $result = $this->sliderRepo->update_item($request->all(),$request->file('slider_image'));
        return redirect('/admin/slider/all-slider');
    }
    public function update_status_item(Request $request){
        $result = $this->sliderRepo->update_status($request->all());
    }
    public function search_items(Request $request){
        $result =  $this->sliderRepo->searchIDorName($request->key_sreach);
        $output = $this->sliderRepo->output_item($result);
        echo $output;
    }
    public function move_to_bin(Request $request){
        $this->sliderRepo->move_bin($request->slider_id);
        return redirect('/admin/slider/all-slider');
    }

    public function count_bin(){
        $result = $this->sliderRepo->count_bin();
        echo $result;
    }
    public function list_bin(){
        $items = $this->sliderRepo->getItemBinByPaginate(3);
        return view('admin.Slider.soft_deleted_slider')->with(compact('items'));
    }
    function load_bin(){
        $sliders =  $this->sliderRepo->getItemBinByPaginate(3);
        $output = $this->sliderRepo->output_item_bin($sliders);
        echo $output;
    }
    public function search_bin(Request $request){
        $output = $this->sliderRepo->search_bin($request->key_sreach);
        echo $output;
    }
    public function bin_delete(Request $request)
    {
        $result =  $this->sliderRepo->delete_item($request->slider_id);
    }

    public function un_bin(Request $request)
    {
        $result =  $this->sliderRepo->restore_item($request->slider_id);
    }
}