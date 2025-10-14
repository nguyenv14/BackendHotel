<?php
namespace App\Repositories\CustomerRepository;

use App\Repositories\RepositoryInterface;

interface CustomerRepositoryInterface extends RepositoryInterface
{
    public function getAllByPaginate($value);
    public function searchNameOrEmail($key);
    public function update_status($data);
    public function delete_customer($id);
    public function output_customer($customers);
    public function output_sort_type($customers,$type);
    public function output_customer_system($customers);
    public function binByPaginate($value);
    public function selected_email($data);
    public function send_mail($to_name,$to_email,$title_email,$content_email);
    public function validate_email($email);
    public function count_bin();
    public function output_garbage_can($customers);
    public function output_search_bin($key);
    public function restore_item($id);
    public function delete_item($id);
}
