<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\GalleryHotelRepository\GalleryHotelRepositoryInterface;
use App\Models\Hotel;
use Session;
session_start();

class GalleryHotelController extends Controller
{
     /**
     * @var PostRepositoryInterface|\App\Repositories\Repository
     */
    protected $galleryRepo;

    public function __construct(GalleryHotelRepositoryInterface $galleryRepo)
    {
        $this->galleryRepo = $galleryRepo;
    }
    public function list_media(Request $request){
        $hotel =  $this->galleryRepo->findHotel($request->hotel_id);
        $video = $this->galleryRepo->getGalleryByPaginate($request->hotel_id,2,999);
        $images = $this->galleryRepo->getGalleryByPaginate($request->hotel_id,1,999);
        return view('admin.Hotel.ManagerHotel.GalleryMedia.media_gallery')->with(compact('hotel','video','images'));
    }
    public function list_video(Request $request){
        $hotel =  $this->galleryRepo->findHotel($request->hotel_id);
        $gallery = $this->galleryRepo->getGalleryByPaginate($request->hotel_id,2,5);
        return view('admin.Hotel.ManagerHotel.GalleryMedia.add_video_hotel')->with(compact('gallery','hotel'));
    }

    public function list_image(Request $request){
        $hotel =  $this->galleryRepo->findHotel($request->hotel_id);
        $gallery = $this->galleryRepo->getGalleryByPaginate($request->hotel_id,1,5);
        return view('admin.Hotel.ManagerHotel.GalleryMedia.add_image_hotel')->with(compact('gallery','hotel'));
    }

    public function load_items(Request $request){
        $items = $this->galleryRepo->getGalleryByPaginate($request->hotel_id,$request->type,5);
        $output =  $this->galleryRepo->output_item($items,$request->hotel_id,$request->type);
        echo $output;
    }
    
    public function insert_items(Request $request){
        $result = $this->galleryRepo->insert_item($request->hotel_id,$request->file('file'), $request->type);
    }
    
    public function delete_item(Request $request){
        $result = $this->galleryRepo->delete_item($request->gallery_id);
    }
    public function update_content(Request $request)
    {
        $result = $this->galleryRepo->update_content($request->gallery_id,$request->gallery_content);
    }
    public function update_name(Request $request)
    {
        $result = $this->galleryRepo->update_name($request->gallery_id,$request->gallery_name);
    }

    public function update_image(Request $request)
    {
        $result = $this->galleryRepo->update_image($request->gallery_id,$request->file('file'));
    }
}