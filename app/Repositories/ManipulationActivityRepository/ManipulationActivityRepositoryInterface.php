<?php
namespace App\Repositories\ManipulationActivityRepository;

use App\Repositories\RepositoryInterface;

interface ManipulationActivityRepositoryInterface extends RepositoryInterface
{
    public function getAllManiAdminByPaginate($value);
    public function getAllManiCustomerByPaginate($value);
}