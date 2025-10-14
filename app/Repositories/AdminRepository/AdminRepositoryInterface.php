<?php
namespace App\Repositories\AdminRepository;

use App\Repositories\RepositoryInterface;

interface AdminRepositoryInterface extends RepositoryInterface
{
    public function getAllByPaginate($value);
    public function assign_roles($data);
    public function delete_admin_roles($admin_id);
    public function update_admin($data);
    public function searchNameOrEmail($key);
    public function output_admin($admins);
}
