<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\GalleryRestaurant;
use App\Models\MenuRestaurant;
use App\Models\Restaurant;
use App\Models\TableRestaurant;
use Illuminate\Http\Request;

class RestaurantsController extends Controller
{

    public function index()
    {
        $items = Restaurant::paginate(5);
        return view('admin.Restaurants.manager_restaurants')->with('items', $items);
    }

    public function load_restaurants(Request $request)
    {
        $restaurant = Restaurant::paginate(5);
        return $this->output_item($restaurant);
    }


    public function add_restaurant(Request $request)
    {
        $areas = Area::query()->get();
        return view('admin.Restaurants.add_restaurants')->with('areas', $areas);
    }

    public function save_restaurant(Request $request)
    {
        $data = $request->all();
        unset($data['_token']);
        $image = $request->file('restaurant_image');

        if ($image != null) {
            $get_image_name = $image->getClientOriginalName(); /* Lấy Tên File */
            $image_name = current(explode('.', $get_image_name));
            $new_image = $image_name . rand(0, 99) . '.' . $image->getClientOriginalExtension(); /* getClientOriginalExtension() hàm lấy phần mở rộng của ảnh */
            $image->move('public/fontend/assets/img/restaurant/', $new_image);
            $data['restaurant_image'] = $new_image;
        }
        Restaurant::query()->create($data);
        $this->message("success", 'Thêm Mới Nhà Hàng Thành Công!');
        return redirect('/admin/restaurants/all-restaurants');
    }

    public function edit_restaurant(Request $request)
    {
        $restaurant = Restaurant::query()->where('restaurant_id', $request->restaurant_id)->first();
        $areas = Area::query()->get();
        return view('admin.Restaurants.edit_restaurant')->with('restaurant', $restaurant)->with('areas', $areas);
    }

    public function update_restaurant(Request $request)
    {
        $data = $request->all();
        unset($data['_token']);
        $get_image = $request->file('restaurant_image');
        if ($get_image) {
            $get_image_name = $get_image->getClientOriginalName(); /* Lấy Tên File */
            $image_name = current(explode('.', $get_image_name)); /* VD Tên File Là nhan.jpg thì hàm explode dựa vào dấm . để phân tách thành 2 chuổi là nhan và jpg , còn hàm current để chuổi đầu , hàm end thì lấy cuối */
            $new_image = $image_name . rand(0, 99) . '.' . $get_image->getClientOriginalExtension(); /* getClientOriginalExtension() hàm lấy phần mở rộng của ảnh */
            $get_image->move('public/fontend/assets/img/restaurant/', $new_image);
            $data['restaurant_image'] = $new_image;
            $image_old = Restaurant::query()->select('restaurant_image')->where('restaurant_id', $data['restaurant_id'])->first();
            unlink('public/fontend/assets/img/restaurant/' . $image_old->restaurant_image);
        }
        Restaurant::query()->upsert($data, 'restaurant_id');
        $this->message("success", "Cập Nhật Nhà Hàng Thành Công!");
        return redirect('/admin/restaurants/all-restaurants');
    }

    public function insert_table(Request $request)
    {
        $data = $request->all();
        unset($data['_token']);
        $data['table_status'] = 1;
        TableRestaurant::query()->create($data);
        return back();
    }

    public function insert_menu(Request $request)
    {
        $data = $request->all();
        unset($data['_token']);
        $get_image = $request->file('file');
        if ($get_image) {
            $get_image_name = $get_image->getClientOriginalName(); /* Lấy Tên File */
            $image_name = current(explode('.', $get_image_name)); /* VD Tên File Là nhan.jpg thì hàm explode dựa vào dấm . để phân tách thành 2 chuổi là nhan và jpg , còn hàm current để chuổi đầu , hàm end thì lấy cuối */
            $new_image = $image_name . rand(0, 99) . '.' . $get_image->getClientOriginalExtension(); /* getClientOriginalExtension() hàm lấy phần mở rộng của ảnh */
            $get_image->move('public\fontend\assets\img\menu', $new_image);
            $data['menu_item_image'] = $new_image;
        }

        $data['menu_item_status'] = 1;
        MenuRestaurant::query()->create($data);
        return back();
    }

    public function delete_item(Request $request)
    {
        $data = $request->all();
        if ($data['status']) {
            TableRestaurant::query()->where('table_id', $data['id'])->delete();
        } else {
            MenuRestaurant::query()->where('menu_item_id', $data['id'])->delete();
        }
        return back();
    }

    public function delete_restaurant(Request $request)
    {

    }

    public function restaurant_detail(Request $request)
    {
        $restaurant = Restaurant::query()->where('restaurant_id', $request->restaurant_id)->first();
        $menuRestaurant = MenuRestaurant::query()->where('restaurant_id', $request->restaurant_id)->get();
        $galleryRestaurant = GalleryRestaurant::query()->where('restaurant_id', $request->restaurant_id)->get();
        $tableRestaurant = TableRestaurant::query()->where('restaurant_id', $request->restaurant_id)->get();
        return view('admin.Restaurants.manage_restaurantdetail')
            ->with('restaurant', $restaurant)
            ->with('menus', $menuRestaurant)
            ->with('gallerys', $galleryRestaurant)
            ->with('tables', $tableRestaurant);
    }

    public function insert_gallery(Request $request)
    {
        $product_id = $request->restaurant_id;
        $get_images = $request->file('file');

        if ($get_images) {
            foreach ($get_images as $get_image) {
                $get_image_name = $get_image->getClientOriginalName(); /* Lấy Tên File */
                $image_name = current(explode('.', $get_image_name)); /* VD Tên File Là nhan.jpg thì hàm explode dựa vào dấm . để phân tách thành 2 chuổi là nhan và jpg , còn hàm current để chuổi đầu , hàm end thì lấy cuối */
                $new_image = $image_name . rand(0, 99) . '.' . $get_image->getClientOriginalExtension(); /* getClientOriginalExtension() hàm lấy phần mở rộng của ảnh */
                $get_image->move('public\fontend\assets\img\restaurant', $new_image);

                $gallery = new GalleryRestaurant();
                $gallery['restaurant_id'] = $product_id;
                $gallery['gallery_restaurant_name'] = $image_name;
                $gallery['gallery_restaurant_image'] = $new_image;
                $gallery['gallery_restaurant_content'] = "Ảnh này chưa có nội dung !";
                $gallery->save();
            }
        }
        $this->message("success", "Thêm Vào " . count($get_images) . " Hình Ảnh Vào Thư Viện Thành Công !");
        return back();
    }

    public function loading_gallery(Request $request)
    {

        $gallerys = GalleryRestaurant::query()->where('restaurant_id', $request->restaurant_id)->get();
        $output = '';
        $i = 0;
        foreach ($gallerys as $gallery) {
            $output .= '
            <tr>
                <td>  ' . ++$i . ' </td>
                <td> ' . $gallery->product_id . ' </td>
                <td contentEditable class="update_gallery_product_name"  data-gallery_id = "' . $gallery->gallery_restaurant_id . '"> <div style="width: 100px;overflow: hidden;">  ' . $gallery->gallery_restaurant_name . ' </div>  </td>
                <td>

                <form>
                ' . csrf_field() . '
                <input hidden id="up_load_file' . $gallery->gallery_restaurant_id . '" class="up_load_file"  type="file" name="file_image" accept="image/*" data-gallery_id = "' . $gallery->gallery_restaurant_id . '">
                <label class="up_load_file" for="up_load_file' . $gallery->gallery_restaurant_id . '" > <img style="object-fit: cover" width="40px" height="20px"
                src=' . URL('public/fontend/assets/img/restaurant/' . $gallery->gallery_restaurant_image) . ' alt=""></label>
                </form>
               </td>
                <td  contentEditable  class="edit_gallery_product_content"  data-gallery_id = "' . $gallery->gallery_restaurant_id . '"><div style="width: 200px;overflow: hidden">  ' . $gallery->gallery_restaurant_content . ' </div>  </td>
                <td>';
            $output .= '
                    <button  style="border: none" class="delete_gallery_product" data-gallery_id = "' . $gallery->gallery_restaurant_id . '"><i style="font-size: 22px" class="mdi mdi-delete-sweep text-danger "></i></button>
                    ';
            $output .= '
                </td>
            </a>
            </tr>
            ';
        }
        return $output;
    }


    public function output_item($items)
    {
        $output = '';
        foreach ($items as $key => $restaurant) {
            $output .= '
            <tr>
            <td>' . $restaurant->restaurant_id . '</td>
            <td>' . $restaurant->restaurant_name . '</td>
            ';
            if ($restaurant->restaurant_rank == 1) {
                $output .= '<td>1 <i class="mdi mdi-star"></i></td>';
            } else if ($restaurant->restaurant_rank == 2) {
                $output .= '<td>2 <i class="mdi mdi-star"></i></td>';
            } else if ($restaurant->restaurant_rank == 3) {
                $output .= '<td>3 <i class="mdi mdi-star"></i></td>';
            } else if ($restaurant->restaurant_rank == 4) {
                $output .= '<td>4 <i class="mdi mdi-star"></i></td>';
            } else if ($restaurant->restaurant_rank == 5) {
                $output .= '<td>5 <i class="mdi mdi-star"></i></td>';
            }

            $output .= '
            <td><img style="object-fit: cover" width="40px" height="20px" src="' . URL('public/fontend/assets/img/restaurant/' . $restaurant->restaurant_image) . '"alt=""></td>

            <td> Quận ' . $restaurant->area->area_name . '</td>
            <td>';
            if ($restaurant->restaurant_status == 1) {
                $output .= '
                    <span class = "update-status" data-item_id = "' . $restaurant->restaurant_id . '" data-item_status = "0">
                    <i style="color: rgb(52, 211, 52); font-size: 30px"
                    class="mdi mdi-toggle-switch"></i>
                    </span>
                    ';
            } else {
                $output .= '
                <span class = "update-status" data-item_id = "' . $restaurant->restaurant_id . '" data-item_status = "1" >
                <i style="color: rgb(196, 203, 196);font-size: 30px"
                class="mdi mdi-toggle-switch-off"></i>
                </span>';
            }
            $output .= '
            </td>
            <td>
            <br>
            <a href="' . URL('admin/restaurants/restaurant-detail?restaurant_id=') . $restaurant->restaurant_id . '">
                    <i style="font-size: 20px;padding-right: 5px; color: rgb(230, 168, 24)"
                        class=" mdi mdi-clipboard-outline"></i>
                </a>
            </br>
            <a href="' . URL('admin/restaurants/edit-restaurant?restaurant_id=') . $restaurant->restaurant_id . '">
                    <i style="font-size: 20px" class="mdi mdi-lead-pencil"></i>
            </a>
            </br>
            ';

            $output .= '<button type="button" class="btn-sm btn-gradient-danger btn-icon-text btn-delete-item mt-2" data-item_id = "' . $restaurant->restaurant_id . '">
                <i class="mdi mdi-delete-forever btn-icon-prepend"></i> Xóa </button>';

            $output .= '
    </td>
        </tr>
            ';
        }
        return $output;
    }

    public function message($type, $content)
    {
        $message = array(
            "type" => "$type",
            "content" => "$content",
        );
        session()->flash('message', $message);
    }
}
