@extends('admin.admin_layout')
@section('admin_content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-crosshairs-gps"></i>
            </span> Quản Lý Footer
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
                    <div class="card-title col-sm-9">Bảng Danh Sách Cấu Hình
                    </div>
                    <div class="col-sm-3">
                    </div>
                </div>

                <table class="table table-bordered">
                    
                    <tbody>
                       
                        @if ($info == null)
                            <tr>
                                <th>Tên Công Ty/Shop</th>
                                <td contenteditable id="name" class="company_edit"></td>
                            </tr>
                            <tr>
                                <th>Tổng Đài Chăm Sóc</th>
                                <td contenteditable id="call" class="company_edit"></td>
                            </tr>
                            <tr>
                                <th>Email Đơn Vị</th>
                                <td contenteditable id="email" class="company_edit"></td>
                            </tr>
                            <tr>
                                <th>Địa Chỉ</th>
                                <td contenteditable id="dress" class="company_edit"></td>
                            </tr>
                            <tr>
                                <th>Slogan Công Ty</th>
                                <td contentEditable id="slogan" class="company_edit">
                                    <div class="company_edit" style="width: 660px;overflow: hidden"></div>
                                </td>
                            </tr>
                            <tr>
                                <th>Copyright</th>
                                <td contentEditable id="copyright" class="company_edit">
                                    <div class="company_edit" style="width: 660px;overflow: hidden"></div>
                                </td>
                            </tr>
                        @else
                        <tr>
                            <th>Tên Công Ty/Shop</th>
                            <td contenteditable id="name" class="company_edit">
                                {{ $info->company_name }}</td>
                        </tr>
                        <tr>
                            <th>Tổng Đài Chăm Sóc</th>
                            <td contenteditable id="call" class="company_edit">
                                {{ $info->company_hostline }}</td>
                        </tr>
                        <tr>
                            <th>Email Đơn Vị</th>
                            <td contenteditable id="email" class="company_edit">
                                {{ $info->company_mail }}</td>
                        </tr>
                        <tr>
                            <th>Địa Chỉ</th>
                            <td contenteditable id="dress" class="company_edit">
                                {{ $info->company_address }}</td>
                        </tr>
                        <tr>
                            <th>Slogan Công Ty</th>
                            <td contentEditable id="slogan" class="company_edit">
                                <div class="company_edit" style="width: 660px;overflow: hidden">
                                    {{ $info->company_slogan }}</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Copyright</th>
                            <td contentEditable id="copyright" class="company_edit">
                                <div class="company_edit" style="width: 660px;overflow: hidden">
                                    {{ $info->company_copyright }}</div>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.table-bordered tbody').on('blur', '.company_edit', function() {
                var name = $('#name').text();
                var call = $('#call').text();
                var email = $('#email').text();
                var dress = $('#dress').text();
                var slogan = $('#slogan').text();
                var copyright = $('#copyright').text();
                $.ajax({
                    url: '{{ url('admin/config-footer/edit-content-footer') }}',
                    method: 'get',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        company_name:name,
                        company_hostline:call,
                        company_mail:email,
                        company_address:dress,
                        company_slogan:slogan,
                        company_copyright:copyright,
                    },

                    success: function(data) {
                        message_toastr("success", "Cập nhập thông tin trang web thành công")
                    },
                    error: function(data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });

            })

        });
    </script>
@endsection
