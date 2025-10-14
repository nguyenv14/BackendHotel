<?php
namespace App\Repositories\ActivityLogRepository;

use App\Repositories\BaseRepository;

class ActivityLogRepository extends BaseRepository implements ActivityLogRepositoryInterface
{
    //lấy model tương ứng
    public function getModel()
    {
        return \App\Models\ActivityLog::class;
    }
    public function getAllAdminByPaginate($value){
        return $this->model::where('activitylog_type', 0)->orderby('activitylog_id', 'DESC')->Paginate($value);
    }
    public function getAllCustomerByPaginate($value){
        return $this->model::where('activitylog_type', 1)->orderby('activitylog_id', 'DESC')->Paginate($value);
    }
}