@extends('admin.admin_layout')
@section('admin_content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-book-variant"></i>
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

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div style="display: flex;justify-content: space-between">
                    <div class="card-title col-sm-9">Bảng Danh Sách Admin</div>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <input id="search" type="text" class="form-control" name="search"
                                placeholder="Tìm Kiếm Tên Admin Hoặc Email">
                            <button type="button" class="btn-md btn-inverse-success btn-icon">
                                <i class="mdi mdi-account-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <table style="margin-top:20px " class="table table-bordered">
                    <thead>
                        <tr>
                            <th> #ID </th>
                            <th> Tên Người Dùng </th>
                            <th> Số Điện Thoại </th>
                            <th> Email </th>
                            <th> Password </th>
                            <th> Quản Trị </th>
                            <th> Quản Lý </th>
                            <th> Nhân Viên </th>
                            <th> Thao Tác </th>
                        </tr>
                    </thead>
                    <tbody id="load_table_admin">
                        {{-- @foreach ($admins as $key => $admin)
                            <tr>
                                <td>{{ $admin->admin_id }}</td>
                                <td>{{ $admin->admin_name }}</td>
                                <td>{{ $admin->admin_phone }}</td>
                                <td>{{ $admin->admin_email }}</td>
                                <td><div style="width: 120px; text-overflow:ellipsis;overflow: hidden">{{ $admin->admin_password }}</div></td>
                                <td>
                                    <div class="form-check form-check-success">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input"
                                                name="roles{{ $admin->admin_id }}"
                                                {{ $admin->hasRoles('admin') ? 'checked' : '' }} value="1"
                                                data-admin_id="{{ $admin->admin_id }}">
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check form-check-success">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input"
                                                name="roles{{ $admin->admin_id }}"
                                                {{ $admin->hasRoles('manager') ? 'checked' : '' }} value="2"
                                                data-admin_id="{{ $admin->admin_id }}">
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check form-check-success">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input"
                                                name="roles{{ $admin->admin_id }}"
                                                {{ $admin->hasRoles('employee') ? 'checked' : '' }} value="3"
                                                data-admin_id="{{ $admin->admin_id }}">
                                        </label>
                                    </div>
                                </td>

                                <td>
                                    <div style="margin-top: 10px">
                                        <button type="button" class="btn-sm btn-gradient-dark btn-rounded btn-fw btn-delete-admin-roles" data-admin_id="{{ $admin->admin_id }}">Xóa Quyền 
                                        </button>
                                    </div>
                                    <div style="margin-top: 10px">
                                        <a href="{{ url('admin/auth/impersonate?admin_id=' . $admin->admin_id) }}"><button
                                                type="button" class="btn-sm btn-gradient-info btn-rounded btn-fw">Chuyển
                                                Quyền</button>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- Phân Trang Bằng Paginate + Boostraps , Apply view Boostrap trong Provider --}}
    {{-- <nav aria-label="Page navigation example"> --}}
    {!! $admins->links('admin.pagination') !!}
    {{-- </nav> --}}
    {{-- Phân Trang Bằng Ajax --}}
    <script>
        $('.pagination a').unbind('click').on('click', function(e) {
            e.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            getPosts(page);
        });

        function getPosts(page) {
            $.ajax({
                url: '{{ url('admin/auth/load-table-admin?page=') }}' + page,
                method: 'get',
                data: {

                },
                success: function(data) {
                    $('#load_table_admin').html(data);
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        }
    </script>


    <script>
        loading_table_admin();

        function loading_table_admin() {
            $.ajax({
                url: '{{ url('admin/auth/load-table-admin') }}',
                method: 'get',
                data: {},
                success: function(data) {
                    $('#load_table_admin').html(data);
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        }
    </script>
    <script>
        $(document).on('click', '.form-check-input', function() {
            // $('.form-check-input').click(function() {
            var admin_id = $(this).data('admin_id');
            var index_roles = $('input[type="radio"][name="roles' + admin_id + '"]:checked').val();
            var _token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '{{ url('admin/auth/assign-roles') }}',
                method: 'POST',
                data: {

                    admin_id: admin_id,
                    index_roles: index_roles,
                    _token: _token,

                },
                success: function(data) {
                    loading_table_admin()
                    if (data == "admin") {
                        message_toastr("success", "Cấp Quyền Quản Trị Thành Công!", "Thông báo");
                    } else if (data == "manager") {
                        message_toastr("success", "Cấp Quyền Quản Lý Thành Công!", "Thông báo");
                    } else if (data == "employee") {
                        message_toastr("success", "Cấp Quyền Nhân Viên Thành Công!", "Thông báo");
                    } else if (data = "permission_error") {
                        message_toastr("warning",
                            "Quản Trị Viên Không Thể Tự Cấp Lại Quyền Cho Chính Mình!",
                            "Oh Noooooo!");
                    }
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })

        });
        $(document).on('click', '.btn-delete-admin-roles', function() {
            // $('.btn-delete-admin-roles').click(function(){
            var admin_id = $(this).data('admin_id');
            var _token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '{{ url('admin/auth/delete-admin-roles') }}',
                method: 'get',
                data: {
                    admin_id: admin_id,
                    _token: _token,
                },
                success: function(data) {
                    loading_table_admin()
                    if (data == "error_delete_admin") {
                        message_toastr("warning",
                            "Quản Trị Viên Không Thể Tự Xóa Chính Mình!",
                            "Oh Noooooo!");
                    } else if (data == "true") {
                        message_toastr("success", "Đã Xóa Thành Công!", "Thông báo");
                    }
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })

        });

        $('#search').keyup(function() {

            var key_sreach = $(this).val();

            $.ajax({
                url: '{{ url('/admin/auth/all-admin-sreach') }}',
                method: 'GET',
                data: {
                    key_sreach: key_sreach,
                },
                success: function(data) {
                    $('#load_table_admin').html(data);
                },
                error: function() {
                    alert("Bug Huhu :<<");
                }
            })
        });
    </script>
@endsection
