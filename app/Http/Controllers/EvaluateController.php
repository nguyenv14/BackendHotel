<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\EvaluateRepository\EvaluateRepositoryInterface;

use Session;
session_start();

class EvaluateController extends Controller
{
     /**
     * @var PostRepositoryInterface|\App\Repositories\Repository
     */ 
    protected $evaluate;
    public function __construct(EvaluateRepositoryInterface $evaluate)
    {
        $this->evaluate = $evaluate;
    }
    public function list_items(){
        $items = $this->evaluate->getAllByPaginate(5);
        return view('admin.Evaluate.all_evaluate')->with(compact('items'));
    }
    public function load_items(){
        $items = $this->evaluate->getAllByPaginate(5);
        $output =  $this->evaluate->output_item($items);
        echo $output;
    }

    public function search_items(Request $request){
        $result =  $this->evaluate->searchIDorName($request->key_sreach);
        $output = $this->evaluate->output_item($result);
        echo $output;
    }
    public function move_to_bin(Request $request){
        $this->evaluate->move_bin($request->evaluate_id);
        return redirect('admin/evaluate/all-evaluate');
   }

    public function count_bin(){
        $result = $this->evaluate->count_bin();
        echo $result;
    }
    public function list_bin(){
        $items = $this->evaluate->getItemBinByPaginate(5);
        return view('admin.Evaluate.soft_deleted_evaluate')->with(compact('items'));
    }
    function load_bin(){
        $evaluates =  $this->evaluate->getItemBinByPaginate(5);
        $output = $this->evaluate->output_item_bin($evaluates);
        echo $output;
    }
    public function search_bin(Request $request){
        $output = $this->evaluate->search_bin($request->key_sreach);
        echo $output;
    }
    public function bin_delete(Request $request)
    {
        $result =  $this->evaluate->delete_item($request->evaluate_id);
    }

    public function un_bin(Request $request)
    {
        $result =  $this->evaluate->restore_item($request->evaluate_id);
    }
}