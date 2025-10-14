<?php
namespace App\Repositories\ConfigWebRepository;

use App\Repositories\RepositoryInterface;

interface ConfigWebRepositoryInterface extends RepositoryInterface
{
   public function get_logo();
   public function get_slogan();
   public function get_brand();
   public function output_slogan($config_slogan);
   public function output_logo($config_logo);
   public function output_brand($config_brands);
   public function update_config($data);
   public function delete_config($id);
   public function update_img($config_id,$get_image);

}