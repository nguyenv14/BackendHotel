@extends('admin.Hotel.ManagerHotel.manager_hotel_layout')
@section('manager_hotel')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-account-plus"></i>
            </span> Thêm Mới Nhân Viên (Staff)
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="mdi mdi-timetable"></i>
                    <span>{{ date('d/m/Y') }}</span>
                </li>
            </ul>
        </nav>
    </div>

    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Thông Tin Nhân Viên Mới</h4>
                <form class="forms-sample" action="{{ url('admin/hotel/manager/staff/save-staff') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="hotel_id" value="{{ request()->get('hotel_id') }}">
                    <div class="form-group mb-3">
                        <label for="admin_name">Họ và Tên <span class="text-danger">*</span></label>
                        <input type="text" name="admin_name" class="form-control" id="admin_name"
                            placeholder="Nhập họ và tên nhân viên" value="{{ old('admin_name') }}" required>
                    </div>
                    @error('admin_name')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <div class="form-group mb-3">
                        <label for="admin_email">Email <span class="text-danger">*</span></label>
                        <input type="email" name="admin_email" class="form-control" id="admin_email"
                            placeholder="Nhập email hợp lệ" value="{{ old('admin_email') }}" required>
                    </div>
                    @error('admin_email')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <div class="form-group mb-3">
                        <label for="admin_phone">Số Điện Thoại</label>
                        <input type="text" name="admin_phone" class="form-control" id="admin_phone"
                            placeholder="Nhập số điện thoại (không bắt buộc)" value="{{ old('admin_phone') }}">
                    </div>
                    @error('admin_phone')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <div class="form-group mb-3">
                        <label for="admin_password">Mật Khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="admin_password" class="form-control" id="admin_password"
                            placeholder="Tối thiểu 6 ký tự" minlength="6" required value="{{ old('admin_password') }}">
                    </div>
                    @error('admin_password')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <div class="form-group mb-3">
                        <label for="admin_password_confirmation">Xác Nhận Mật Khẩu <span
                                class="text-danger">*</span></label>
                        <input type="password" name="admin_password_confirmation" class="form-control"
                            id="admin_password_confirmation" placeholder="Nhập lại mật khẩu" required
                            value="{{ old('admin_password_confirmation') }}">

                    </div>
                    @error('admin_password_confirmation')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <!-- Nếu cần chọn vai trò -->
                    {{-- <div class="form-group mb-3">
                    <label>Vai Trò</label>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="role" value="employee" checked> Nhân Viên
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="role" value="manager"> Quản Lý
                        </label>
                    </div>
                </div> --}}

                    <button type="submit" class="btn btn-gradient-primary me-2">
                        <i class="mdi mdi-content-save me-1"></i> Lưu Nhân Viên
                    </button>
                    <a href="{{ url()->previous() }}" class="btn btn-light">
                        <i class="mdi mdi-arrow-left me-1"></i> Hủy
                    </a>
                </form>
            </div>
        </div>
    </div>
@endsection
