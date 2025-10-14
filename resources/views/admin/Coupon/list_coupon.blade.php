@extends('admin.admin_layout')
@section('admin_content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-certificate"></i>
            </span> Quản Lý Mã Giảm Giá
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

    <?php
    $mesage = Session::get('mesage');
    if ($mesage) {
        echo $mesage;
        Session::put('mesage', null);
    }
    ?>
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div style="display: flex;justify-content: space-between">
                    <div class="card-title col-sm-9">Bảng Danh Sách Mã Giảm Giá</div>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-gradient-primary me-2">Tìm kiếm</button>
                            </span>
                        </div>
                    </div>
                </div>
                <table style="margin-top:20px " class="table table-bordered">
                    <thead>
                        <tr>
                            <th> #ID </th>
                            <th> Tên Mã Giảm Giá </th>
                            <th> Mã Giảm Giá </th>
                            {{-- <th> Mô Tả </th> --}}
                            <th> Tính Năng </th>
                            <th> Số Lượng  </th>
                            <th> Giảm Giá</th>
                            <th> Ngày Bắt Đầu </th>
                            <th> Ngày Kết Thúc</th>
                            <th> Thao Tác </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coupon as $key => $coupon)
                            <tr>
                                <td>{{ $coupon->coupon_id }}</td>
                                <td>{{ $coupon->coupon_name }}</td>
                                <td>{{ $coupon->coupon_name_code }}</td>
                                {{-- <td>{{ $coupon->coupon_desc }}</td> --}}
                                <td>
                                    @if ($coupon->coupon_condition == 1)
                                        Giảm Giá Theo %
                                    @else
                                        Giảm Theo Giá Tiền
                                    @endif
                                </td>
                                <td>{{ $coupon->coupon_qty_code }}</td>
                                <td>
                                    @if ($coupon->coupon_condition == 1)
                                    {{ $coupon->coupon_price_sale .'%'  }}
                                    @else
                                    {{ $coupon->coupon_price_sale ."VNĐ" }}
                                    @endif
                                </td>
                                
                                <td>{{ $coupon->coupon_start_date }}</td>
                                <td>{{ $coupon->coupon_end_date }}</td>
                       
                                <td>
                                    <a href="{{ URL::to('admin/coupon/edit-coupon?coupon_id=' . $coupon->coupon_id) }}">
                                        <i style="font-size: 20px" class="mdi mdi-lead-pencil"></i>
                                    </a>
                                    <a onclick="return confirm('Bạn muốn xóa danh mục này không?')"
                                        href="{{ URL::to('admin/coupon/delete-coupon?coupon_id=' . $coupon->coupon_id) }}"
                                        style="margin-left: 14px">
                                        <i style="font-size: 22px" class="mdi mdi-delete-sweep text-danger "></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
