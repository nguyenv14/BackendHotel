<?php
namespace App\Repositories\EvaluateRepository;

use App\Repositories\BaseRepository;
use App\Models\ManipulationActivity;
use Auth;
class EvaluateRepository extends BaseRepository implements EvaluateRepositoryInterface
{
    //lấy model tương ứng
    public function getModel()
    {
        return \App\Models\Evaluate::class;
    }
    public function getAllByPaginate($value){
        ManipulationActivity::noteManipulationAdmin( "Xem Danh Sách Đánh Giá Khách Sạn");
        return $this->model->paginate($value);
    }
    public function searchIDorName($key){
        ManipulationActivity::noteManipulationAdmin( "Tìm Kiếm Danh Sách Đánh Giá Khách Sạn");
        if($key){
            $result = $this->model::where('evaluate_id', 'like', '%' . $key . '%')->orwhere('customer_name', 'like', '%' . $key . '%')->get();
            return $result;
        }else{
            $result = $this->model->take(5)->get();
            return $result;
        }
    }

    public function move_bin($id){
        $this->delete($id);
        ManipulationActivity::noteManipulationAdmin( "Chuyển Đánh Giá Khách Sạn( ID : ".$id.") Vào Thùng Rác");
    }


    public function count_bin(){
        $result = $this->model->onlyTrashed()->count();
        return $result;
    }
    public function getItemBinByPaginate($value)
    {
        $result = $this->model->onlyTrashed()->orderby('created_at', 'desc')->paginate($value);
        ManipulationActivity::noteManipulationAdmin( "Xem Đánh Giá Khách Sạn Trong Thùng Rác");
        return $result;
    }
    public function search_bin($key){
        if($key){
            $result = $this->model::onlyTrashed()->where(function ($query) use( $key ) {
                $query->where('customer_name', 'like', '%' . $key . '%')
                ->orwhere('evaluate_id', 'like', '%' . $key . '%');
            })->get();
            
            $output =  $this->output_item_bin($result);
            return $output;
        }else{
            $result =  $this->model::onlyTrashed()->get();
            $output = $this->output_item_bin($result);
            return $output;
        }
       
    }
    public function restore_item($id){
        $result = $this->model->withTrashed()->find($id);
        $result->restore();
        ManipulationActivity::noteManipulationAdmin( "Khôi Phục Đánh Giá Khách Sạn ( ID : ". $id.")");
        return true;
    }
    public function delete_item($id){
        $result = $this->model->withTrashed()->find($id);
        $result->forceDelete();
        ManipulationActivity::noteManipulationAdmin( "Xóa Vĩnh Viển Đánh Giá Khách Sạn ( ID : ". $id.")");
        return true;
    }
    
    public function output_item($items){
        $output = '';
        foreach ($items as $key => $evaluate){
            $output .= '
            <tr>
            <td>'.$evaluate->evaluate_id.'</td>
            <td>'.$evaluate->customer_name.'</td> 
            <td>'.$evaluate->show_evalute_hotel($evaluate->hotel_id).'</td> 
            <td>'.$evaluate->evaluate_title.'</td>
            <td>'.$evaluate->evaluate_content.'</td>
            <td>'.$evaluate->evaluate_loaction_point.'</td> 
            <td>'.$evaluate->evaluate_service_point.'</td>
            <td>'.$evaluate->evaluate_price_point.'</td> 
            <td>'.$evaluate->evaluate_sanitary_point.'</td>
            <td>'.$evaluate->evaluate_convenient_point.'</td> 
            <td>'.$evaluate->created_at.'</td> 
            <td>
            </br>';
            if(Auth::user()->hasAnyRoles(['admin','manager'])) {
                $output .= '<button type="button" class="btn-sm btn-gradient-danger btn-icon-text btn-delete-item mt-2" data-item_id = "'. $evaluate->evaluate_id.'">
                <i class="mdi mdi-delete-forever btn-icon-prepend"></i> Xóa </button>';
            }
            $output .= '
            </td>
        </tr>
            ';
        }
        return $output;
    }
    public function output_item_bin($items)
    {
        $output = '';
        foreach ($items as $key => $evaluate) {
            $output .= '
            <tr>
            <td>'.$evaluate->evaluate_id.'</td>
            <td>'.$evaluate->customer_name.'</td> 
            <td>'.$evaluate->show_evalute_hotel($evaluate->hotel_id).'</td> 
            <td>'.$evaluate->evaluate_title.'</td>
            <td>'.$evaluate->evaluate_content.'</td>
            <td>'.$evaluate->evaluate_loaction_point.'</td> 
            <td>'.$evaluate->evaluate_service_point.'</td>
            <td>'.$evaluate->evaluate_price_point.'</td> 
            <td>'.$evaluate->evaluate_sanitary_point.'</td>
            <td>'.$evaluate->evaluate_convenient_point.'</td> 
            <td>'.$evaluate->created_at.'</td> 
            <td>'.$evaluate->deleted_at.'</td>
            <td>
                <button type="button" class="btn-sm btn-gradient-success btn-icon-text btn-restore-item" data-item_id = "'. $evaluate->evaluate_id.'">
                <i class="mdi mdi-backup-restore btn-icon-prepend"></i> Khôi Phục </button>
                </br>
                <button type="button" class="btn-sm btn-gradient-danger btn-icon-text mt-3 btn-delete-item" data-item_id = "'. $evaluate->evaluate_id.'">
                <i class="mdi mdi-delete-forever btn-icon-prepend"></i> Xóa </button>
            </td>
        </tr>
            ';
        }
        return $output;
    }

    public function message($type,$content){
        $message = array(
            "type" => "$type",
            "content" => "$content",
        ); 
        session()->flash('message', $message);
    }
}