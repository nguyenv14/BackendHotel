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
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 style="margin-top: -15px" class="card-title">Thêm Mã Giảm Giá</h4>
            <form class="forms-sample" action="{{ ('save-coupon') }}" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="exampleInputName1">Tên Mã Giảm Giá</label>
                    <input type="text" name="coupon_name" class="form-control" id="" placeholder="Nhập Tên Mã Giảm Giá">
                </div>
                
                <div class="form-group">
                    <label for="exampleInputName1">Mã Giảm Giá</label>
                    <input type="text" name="coupon_name_code" class="form-control" id="" placeholder="Nhập Mã Giảm Giá">
                </div>

                <div class="form-group">
                    <label for="exampleInputName1">Ngày Bắt Đầu</label>
                    <input type="text" name="coupon_start_date" class="form-control" id="coupon_start_date" placeholder="Nhập Ngày Bắt Đầu" value="{{  date('Y-d-m') }}">
                </div>

                <div class="form-group">
                    <label for="exampleInputName1">Ngày Kết Thúc</label>
                    <input type="text" name="coupon_end_date" class="form-control" id="coupon_end_date" placeholder="Nhập Ngày Kết Thúc">
                </div>

                <div class="form-group">
                    <label for="exampleTextarea1">Mô Tả Mã Giảm Giá</label>
                    <textarea rows="8" class="form-control" name="coupon_desc" id=""></textarea>
                </div>

                <div class="form-group">
                    <label for="exampleInputName1">Số Lượng Mã</label>
                    <input type="number" min="1" name="coupon_qty_code" class="form-control" id="" placeholder="Nhập Số Lượng Mã">
                </div>
               
                <div class="form-group">
                    <label for="">Tính Năng Của Mã</label>
                    <select class="form-control" name="coupon_condition">
                        <option value="0">Chọn</option>
                        <option value="1">Giảm Giá Theo %</option>
                        <option value="2">Giảm Giá Theo Số Tiền</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="exampleInputName1">Số Tiền Hoặc Số % Giảm Giá</label>
                    <input type="number" min="1" name="coupon_price_sale" class="form-control" id="" placeholder="Nhập Số Tiền Hoặc Số % Giảm Giá">
                </div>
                <button type="submit" class="btn btn-gradient-primary me-2">Thêm Dô</button>
                <button class="btn btn-light">Cancel</button>
            </form>
        </div>
    </div>
</div>
<script>
     $("#coupon_start_date").datepicker({
        prevText:"Tháng Trước",
        nextText:"Tháng Sau",
        dateFormat:"yy-mm-dd",
        dayNamesMin:["Thứ 2 ", "Thứ 3","Thứ 4","Thứ 5","Thứ 6","Thứ 7","Chủ Nhật"],
        duration:"slow",
    });
    $("#coupon_end_date").datepicker({
        prevText:"Tháng Trước",
        nextText:"Tháng Sau",
        dateFormat:"yy-mm-dd",
        dayNamesMin:["Thứ 2 ", "Thứ 3","Thứ 4","Thứ 5","Thứ 6","Thứ 7","Chủ Nhật"],
        duration:"slow",
    });
</script>  
@endsection
