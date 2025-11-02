@extends('admin.Hotel.ManagerHotel.manager_hotel_layout')
@section('manager_hotel')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-certificate"></i>
            </span> Quản Lý Admin
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
                <h4 style="margin-top: -15px" class="card-title">Chỉnh Sửa Tài Khoản Admin</h4>
                <form class="forms-sample" action="{{ url('admin/hotel/manager/staff/update-staff') }}" method="post"
                    enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="admin_id" value="{{ $admin->admin_id }}">
                    <div class="form-group">
                        <label for="exampleInputName1">Tên Nhân Viên</label>
                        <input type="text" name="admin_name" value="{{ $admin->admin_name }}" class="form-control"
                            id="" placeholder="Nhập tên của nhân viên">
                    </div>
                    @error('admin_name')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <div class="form-group">
                        <label for="admin_email">Email Nhân Viên</label>
                        <input type="text" name="admin_email" value="{{ $admin->admin_email }} "class="form-control"
                            id="" placeholder="Nhập email của nhân viên">
                    </div>
                    @error('admin_email')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <div class="form-group">
                        <label for="admin_phone">Số điện thoại</label>
                        <input type="text" class="form-control" name="admin_phone" id=""
                            value="{{ $admin->admin_phone }}">
                    </div>
                    @error('admin_phone')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <div class="form-group">
                        <label for="admin_password">Mật Khẩu Quản trị</label>
                        <input type="password" name="admin_password" value="" class="form-control" id=""
                            placeholder="Nhập mật khẩu mới">
                    </div>
                    @error('admin_password')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <div class="form-group">
                        <label for="admin_password_confirmation">Mật Khẩu Xác Nhận</label>
                        <input type="password" name="admin_password_confirmation" value="" class="form-control"
                            id="" placeholder="Xác nhận mật khẩu mới">
                    </div>
                    @error('admin_password_confirmation')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <button type="submit" class="btn btn-gradient-primary me-2">Cập Nhật</button>
                    <button class="btn btn-light">Cancel</button>
                </form>
            </div>
        </div>
    </div>
@endsection
