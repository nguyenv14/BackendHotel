<?php
namespace App\Repositories\GalleryHotelRepository;

use App\Repositories\RepositoryInterface;

interface GalleryHotelRepositoryInterface extends RepositoryInterface
{
    public function getGalleryByPaginate($id,$type,$value);
    public function findHotel($id);
    public function output_item($items,$id,$type);
    public function insert_item($id,$get_images,$type);
    public function delete_item($id);
    public function update_content($id,$content);
    public function update_name($id,$name);
    public function update_image($id,$get_image);
    public function message($type,$content);
}
