<?php
namespace App\Repositories\CompanyConfigRepository;

use App\Repositories\BaseRepository;

class CompanyConfigRepository extends BaseRepository implements CompanyConfigRepositoryInterface
{
    public function getModel(){
        return \App\Models\CompanyConfig::class;
    }
    public function getCompany(){
        $result = $this->getFirst();
        return  $result;
    }
    public function InsertorUpdate($data){
        $info = $this->getCompany();
        if($info){
            $this->update($info->company_id,$data);
            return true;
        }else{
            $this->create($data);
            return true;
        }
    }

}