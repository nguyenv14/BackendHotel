<?php
namespace App\Repositories\CompanyConfigRepository;

use App\Repositories\RepositoryInterface;

interface CompanyConfigRepositoryInterface extends RepositoryInterface
{
    public function getCompany();
    public function InsertorUpdate($data);
    
}