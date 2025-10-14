<?php
namespace App\Repositories\GalleryRoomRepository;

use App\Repositories\RepositoryInterface;

interface GalleryRoomRepositoryInterface extends RepositoryInterface
{
    public function getGalleryByPaginate($id,$value);
    public function getAllRomByIDHotel($id);
    public function findRoom($id);
    public function findHotel($id);
    public function insert_item($id,$get_images);
    public function output_item($items,$id);
    public function delete_item($id);
    public function update_content($id,$content);
    public function update_name($id,$name);
    public function update_image($id,$get_image);
    public function message($type,$content);
}
