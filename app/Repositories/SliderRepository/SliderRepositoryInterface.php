<?php
namespace App\Repositories\SliderRepository;

use App\Repositories\RepositoryInterface;

interface SliderRepositoryInterface extends RepositoryInterface
{
    public function getAllByPaginate($value);
    public function output_item($items);
    public function insert_item($data, $get_image);
    public function update_item($data, $get_image);  
    public function update_status($data);
    public function move_bin($id);
    public function searchIDorName($key);
    public function count_bin();
    public function getItemBinByPaginate($value);
    public function search_bin($key);
    public function output_item_bin($items);
    public function restore_item($id);
    public function delete_item($id);
    public function message($type,$content);
}
