@extends('admin.admin_layout')
@section('admin_content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-crosshairs-gps"></i>
            </span> Quản Lý Nhà Hàng
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="mdi mdi-timetable"></i>
                    <span><?php
                          $today = date('d/m/Y');
                          echo $today;
                          ?></span>
                </li>
            </ul>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div style="display: flex;justify-content: space-between">
                    <div class="card-title col-sm-9">Loại Bàn Nhà Hàng {{ $restaurant->restaurant_name }}
                    </div>
                    <div class="col-sm-3">
                    </div>
                </div>

                <form action="{{ URL::to('/admin/restaurants/insert-table') }}"
                      method="post" enctype="multipart/form-data">
                    @csrf
                    <input hidden="hidden" name="restaurant_id" value="{{ $restaurant->restaurant_id }}">
                    <div class="form-group">
                        <label for="exampleTextarea1">Tên bàn</label>
                        <input id="restaurant_placedetails" type="text" name="table_name"
                               class="form-control"
                               placeholder="Tên bàn">
                        <span class="text-danger form-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="exampleTextarea1">Giá phục vụ</label>
                        <input id="restaurant_placedetails" type="text" name="table_price"
                               value="" class="form-control"
                               placeholder="Nhập giá phụ vụ">
                        <span class="text-danger form-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="exampleTextarea1">Số lượng</label>
                        <input id="restaurant_placedetails" type="text" name="table_quantity"
                               value="" class="form-control"
                               placeholder="Nhập số lượng bàn đang có">
                        <span class="text-danger form-message"></span>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>

                <table style="margin-top:20px " class="table table-bordered tab-gallery">
                    <form>
                        {{--                        <input type="hidden" value="{{ $product->product->product_id }}" id="pro_id" name="pro_id">--}}
                        @csrf
                        <thead>
                        <tr>
                            <th> #STT</th>
                            <th>Tên bàn</th>
                            <th>Giá phục vụ</th>
                            <th>Số lượng bạn</th>
                            <th>Status</th>
                            <th>Thao Tác</th>
                        </tr>
                        </thead>
                        <tbody id="loading_table_product">
                        @if(count($tables) > 0)
                            @foreach($tables as $table)
                                <tr>
                                    <td>{{ $table->table_id }}</td>
                                    <td>{{ $table->table_name }}</td>
                                    <td>{{ $table->table_price }}</td>
                                    <td>{{ $table->table_quantity }}</td>
                                    <td>
                                        @if($table->table_status)
                                            <span class="update-status"
                                                  data-item_id="' . $restaurant->restaurant_id . '"
                                                  data-item_status="0">
                    <i style="color: rgb(52, 211, 52); font-size: 30px" class="mdi mdi-toggle-switch"></i>
                    </span>
                                        @else
                                            <span class="update-status"
                                                  data-item_id="' . $restaurant->restaurant_id . '"
                                                  data-item_status="1">
                <i style="color: rgb(196, 203, 196);font-size: 30px"
                   class="mdi mdi-toggle-switch-off"></i>
                </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ URL('admin/restaurants/delete-item?id=' . $table->table_id . "&status=1" ) }} '">
                                            <i class="mdi mdi-delete-forever btn-icon-prepend"></i>
                                        </a></td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6">
                                    Không có table nào!
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </form>
                </table>
            </div>
        </div>
    </div>
    <input id="restaurant_id" hidden="hidden" name="restaurant_id" value="{{ $restaurant->restaurant_id }}">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div style="display: flex;justify-content: space-between">
                    <div class="card-title col-sm-9">Menu Nhà Hàng {{ $restaurant->restaurant_name }}
                    </div>
                    <div class="col-sm-3">
                    </div>
                </div>

                <form action="{{ URL::to('/admin/restaurants/insert-menu') }}"
                      method="post" enctype="multipart/form-data">
                    @csrf
                    <input hidden="hidden" name="restaurant_id" value="{{ $restaurant->restaurant_id }}">
                    <div class="form-group">
                        <label for="exampleTextarea1">Tên Món</label>
                        <input id="restaurant_placedetails" type="text" name="menu_item_name"
                               class="form-control"
                               placeholder="Tên món">
                        <span class="text-danger form-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="exampleTextarea1">Giá món</label>
                        <input id="restaurant_placedetails" type="text" name="menu_item_price"
                               value="" class="form-control"
                               placeholder="Nhập giá phụ vụ">
                        <span class="text-danger form-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="exampleTextarea1">Mô tả món</label>
                        <input id="restaurant_placedetails" type="text" name="menu_item_description"
                               value="" class="form-control"
                               placeholder="Nhập số lượng bàn đang có">
                        <span class="text-danger form-message"></span>
                    </div>
                    <div class="mb-3">
                        <label for="formFile" class="form-label">Thêm Ảnh Vào Thư Viện Ảnh</label>
                        <input class="form-control" type="file" name="file" id="formFile" accept="image/*" multiple>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>

                <table style="margin-top:20px " class="table table-bordered tab-gallery">
                    <form>
                        {{--                        <input type="hidden" value="{{ $product->product->product_id }}" id="pro_id" name="pro_id">--}}
                        @csrf
                        <thead>
                        <tr>
                            <th> #STT</th>
                            <th>Tên món</th>
                            <th>Mô tả món</th>
                            <th>Hình ảnh</th>
                            <th>Giá món</th>
                            <th>Status</th>
                            <th>Thao Tác</th>
                        </tr>
                        </thead>
                        <tbody id="loading_menu_product">
                        @if(count($menus) > 0)
                            @foreach($menus as $menu)
                                <tr>
                                    <td>{{ $menu->menu_item_id }}</td>
                                    <td>{{ $menu->menu_item_name }}</td>
                                    <td>{{ $menu->menu_item_description }}</td>
                                    <td> <img style="object-fit: cover" width="40px" height="20px"
                                              src='{{  URL('public/fontend/assets/img/menu/' . $menu->menu_item_image)   }}' alt=""></td>
                                    <td>{{ $menu->menu_item_price }}</td>
                                    <td>
                                        @if($menu->menu_item_status)
                                            <span class="update-status"
                                                  data-item_id="' . $restaurant->restaurant_id . '"
                                                  data-item_status="0">
                    <i style="color: rgb(52, 211, 52); font-size: 30px" class="mdi mdi-toggle-switch"></i>
                    </span>
                                        @else
                                            <span class="update-status"
                                                  data-item_id="' . $restaurant->restaurant_id . '"
                                                  data-item_status="1">
                <i style="color: rgb(196, 203, 196);font-size: 30px"
                   class="mdi mdi-toggle-switch-off"></i>
                </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ URL('admin/restaurants/delete-item?id=' . $menu->menu_item_id . "&status=0" ) }}">
                                            <i class="mdi mdi-delete-forever btn-icon-prepend"></i>
                                        </a></td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6">
                                    Không có menu nào!
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </form>
                </table>
            </div>
        </div>
    </div>


    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div style="display: flex;justify-content: space-between">
                    <div class="card-title col-sm-9">Thư Viện Ảnh Nhà Hàng
                    </div>
                    <div class="col-sm-3">
                    </div>
                </div>

                <form action="{{ URL::to('/admin/restaurants/insert-gallery/' . $restaurant->restaurant_id) }}"
                      method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="formFile" class="form-label">Thêm Ảnh Vào Thư Viện Ảnh</label>
                        <input class="form-control" type="file" name="file[]" id="formFile" accept="image/*" multiple>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>

                <table style="margin-top:20px " class="table table-bordered tab-gallery">
                    <form>
                        <input type="hidden" value="{{ $restaurant->restaurant_id }}" id="pro_id" name="restaurant_id">
                        @csrf
                        <thead>
                        <tr>
                            <th> #STT</th>
                            <th>Mã Sản Phẩm</th>
                            <th>Tên Ảnh</th>
                            <th>Hình Ảnh</th>
                            <th>Nội Dung Ảnh</th>
                            <th>Thao Tác</th>
                        </tr>
                        </thead>
                        <tbody id="loading_gallery_product">

                        </tbody>
                    </form>
                </table>

            </div>
        </div>
    </div>
    {{-- Toàn Bộ Script Liên Quan Đến Gallery --}}
    <script>
        $(document).ready(function () {
            /* Loading Gallrery On Table */

            load_gallery_product();

            function load_gallery_product() {
                var product_id = $("input[name='restaurant_id']").val();
                var _token = $("input[name='_token']").val();
                $.ajax({
                    url: '{{ url('admin/restaurants/loading-gallery') }}',
                    method: 'post',
                    data: {
                        _token: _token,
                        restaurant_id: product_id
                    },
                    success: function (data) {
                        $('#loading_gallery_product').html(data);
                    },
                    error: function (data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });

            }

            /* Cập Nhật Tên Ảnh Gallery */
            $('.tab-gallery #loading_gallery_product').on('blur', '.update_gallery_product_name', function () {
                var gallery_id = $(this).data('gallery_id');
                var _token = $("input[name='_token']").val();
                var gallery_name = $(this).text();

                $.ajax({
                    url: '{{ url('admin/product/update-nameimg-gallery') }}',
                    method: 'post',
                    data: {
                        _token: _token,
                        gallery_id: gallery_id,
                        gallery_name: gallery_name,
                    },
                    success: function (data) {
                        message_toastr("success", "Tên Ảnh Đã Được Cập Nhật !");
                        load_gallery_product();
                    },
                    error: function (data) {
                        alert("Fix Bug Huhu :<");
                    },
                });

            });

            /* Cập Nhật Nội Dung Ảnh Gallery */
            $('.tab-gallery #loading_gallery_product').on('blur', '.edit_gallery_product_content', function () {
                var gallery_id = $(this).data('gallery_id');
                var _token = $("input[name='_token']").val();
                var gallery_content = $(this).text();

                $.ajax({
                    url: '{{ url('admin/product/update-content-gallery') }}',
                    method: 'post',
                    data: {
                        _token: _token,
                        gallery_id: gallery_id,
                        gallery_content: gallery_content,
                    },
                    success: function (data) {
                        message_toastr("success", "Nội Dung Ảnh Đã Được Cập Nhật !");
                        load_gallery_product();
                    },
                    error: function (data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });

            });


            /* Xóa Gallery */
            $('.tab-gallery #loading_gallery_product').on('click', '.delete_gallery_product', function () {
                var gallery_id = $(this).data('gallery_id');
                var _token = $("input[name='_token']").val();
                $.ajax({
                    url: '{{ url('admin/product/delete-gallery') }}',
                    method: 'post',
                    data: {
                        _token: _token,
                        gallery_id: gallery_id,
                    },
                    success: function (data) {
                        if (data == 'true') {
                            message_toastr("success", "Ảnh Đã Được Xóa !");
                        } else {
                            message_toastr("error", "Chỉ Có Quản Trị Viên Hoặc Quản Lý Mới Có Quyền Xóa Ảnh Này !");
                        }

                        load_gallery_product();
                        load_gallery_product();

                    },
                    error: function (data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });

            });

            $('.tab-gallery #loading_gallery_product').on('change', '.up_load_file', function () {
                var gallery_id = $(this).data('gallery_id');
                var image = document.getElementById('up_load_file' + gallery_id).files[0];
                var form_data = new FormData();
                form_data.append("file", document.getElementById('up_load_file' + gallery_id).files[0]);
                form_data.append("gallery_id", gallery_id);


                $.ajax({
                    url: '{{ url('admin/product/update-image-gallery') }}',
                    method: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: form_data,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) {
                        message_toastr("success", "Cập Nhật Ảnh Thành Công !");
                        load_gallery_product();
                    },
                    error: function (data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });
            });

            $('#formFile').change(function () {
                var error = '';
                var files = $('#formFile')[0].files;

                if (files.length > 20) {
                    error += 'Bạn Không Được Chọn Quá 20 Ảnh';

                } else if (files.length == '') {
                    error += 'Vui lòng chọn ảnh';

                } else if (files.size > 10000000) {
                    error += 'Ảnh Không Được Lớn Hơn 10Mb';
                }

                if (error == '') {

                } else {
                    $('#formFile').val('');
                    message_toastr("error", '' + error + '');
                    return false;
                }

            });

        });
    </script>
@endsection
