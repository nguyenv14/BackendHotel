<?php
namespace App\Repositories\RoomRepository;

use App\Repositories\RepositoryInterface;

interface RoomRepositoryInterface extends RepositoryInterface
{
    public function getAllByPaginate($id,$value);
    public function getHotel($id);
    public function output_item($items);
    public function insert_item($data);
    public function update_item($data);  
    public function update_status($data);
    public function move_bin($id);
    public function searchIDorName($data);
    public function count_bin($id);
    public function getItemBinByPaginate($id,$value);
    public function search_bin($data);
    public function output_item_bin($items);
    public function restore_item($id);
    public function delete_item($id);
    public function message($type,$content);
}
