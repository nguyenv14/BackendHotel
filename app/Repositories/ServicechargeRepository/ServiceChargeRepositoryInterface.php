<?php
namespace App\Repositories\ServiceChargeRepository;

use App\Repositories\RepositoryInterface;

interface ServiceChargeRepositoryInterface extends RepositoryInterface
{
    public function getAllByPaginate($value);
    public function getAllHotel();
    public function output_item($items);
    public function insert_item($data);
    public function update_item($data);  
    public function move_bin($id);
    // public function searchIDorName($key);
    public function count_bin();
    public function getItemBinByPaginate($value);
    // public function search_bin($key);
    public function output_item_bin($items);
    public function restore_item($id);
    public function delete_item($id);
    public function message($type,$content);
}