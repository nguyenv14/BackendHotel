<?php
namespace App\Repositories\ManipulationActivityRepository;

use App\Repositories\BaseRepository;

class ManipulationActivityRepository extends BaseRepository implements ManipulationActivityRepositoryInterface
{
    //lấy model tương ứng
    public function getModel()
    {
        return \App\Models\ManipulationActivity::class;
    }
    public function getAllManiAdminByPaginate($value){
        return $this->model::where('manipulation_activity_type', 0)->orderby('manipulation_activity_id', 'DESC')->Paginate($value);
    }
    public function getAllManiCustomerByPaginate($value){
        return $this->model::where('manipulation_activity_type', 1)->orderby('manipulation_activity_id', 'DESC')->Paginate($value);
    }
}