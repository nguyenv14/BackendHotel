<?php
namespace App\Repositories\ActivityLogRepository;

use App\Repositories\RepositoryInterface;

interface ActivityLogRepositoryInterface extends RepositoryInterface
{
    public function getAllAdminByPaginate($value);
    public function getAllCustomerByPaginate($value);
}
