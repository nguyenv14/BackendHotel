<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\GalleryRoomRepository\GalleryRoomRepositoryInterface;
use App\Models\Room;
use Session;
session_start();

class GalleryRoomController extends Controller
{
     /**
     * @var PostRepositoryInterface|\App\Repositories\Repository
     */
    protected $galleryRepo;

    public function __construct(GalleryRoomRepositoryInterface $galleryRepo)
    {
        $this->galleryRepo = $galleryRepo;
    }

    public function list_image(Request $request){
        $hotel =  $this->galleryRepo->findHotel($request->hotel_id);
        $rooms =  $this->galleryRepo->getAllRomByIDHotel($request->hotel_id);
        return view('admin.Hotel.ManagerHotel.Room.GalleryRoom.list_gallery_room')->with(compact('hotel','rooms'));
    }

    public function load_items(Request $request){
        $items = $this->galleryRepo->getGalleryByPaginate($request->room_id,99);
        $output =  $this->galleryRepo->output_item($items,$request->room_id);
        echo $output;
    }
    
    public function insert_items(Request $request){
        $result = $this->galleryRepo->insert_item($request->room_id,$request->file('file'));
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