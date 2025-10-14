@extends('admin.admin_layout')
@section('admin_content')

    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-crosshairs-gps"></i>
            </span> Gửi Mail Đến Khách Hàng
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

    <div class="row">

        <div class="col-md-12">
            <button type="button" class="btn btn-secondary py-3 mb-4 text-center d-md-none aside-toggler"><i
                    class="mdi mdi-menu mr-0 icon-md"></i></button>
            <div class="card chat-app-wrapper">
                <div class="row mx-0">
                    <div class="col-lg-1 col-md-4 chat-list-wrapper px-0">

                    </div>
                    <div class="col-lg-10 col-md-8 px-0 d-flex flex-column p-3">
                        <div class="chat-container-wrapper">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <button type="button" class="input-group-text" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal"><i class="mdi mdi-attachment icon-sm">
                                            </i></button>
                                    </div>
                                    <input id="listmailcustomer" type="text" class="form-control to_email"
                                        placeholder="Gửi Đến" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <input id="titleemail" type="text" class="form-control file-upload-info title_email"
                                    placeholder="Tiêu Đề">
                            </div>
                            <div class="form-group">
                                <textarea id="editor1" rows="45" class="form-control content_email" name="">
                                Nhập Nội Dung Vào Đây.....

                                </textarea>
                            </div>


                        </div>
                        <div class="chat-text-field mt-auto">
                            <form action="#">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <button type="button" class="input-group-text"><i
                                                class="mdi mdi-emoticon-happy-outline icon-sm"></i></button>
                                    </div>
                                    <input type="text" class="form-control" disabled>
                                    <div class="input-group-append">
                                        <button type="button" class="input-group-text"><i
                                                class="mdi mdi-paperclip icon-sm"></i></button>
                                    </div>
                                    <div class="input-group-append">
                                        <button type="button" class="input-group-text btn-submit-email"><i
                                                class="mdi mdi-send icon-sm"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg- d-none d-lg-block px-0 chat-sidebar">

                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Chọn Người Gửi Đến</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table style="margin-top:20px " class="table table-bordered">
                        <thead>
                            <tr>
                                <th> Chọn </th>
                                <th> Tên Người Dùng </th>
                                <th> Email </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $customer)
                                <tr>
                                    <td>
                                        <div style="margin-left:4px " class="form-check form-check-success">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input id-customer-checked"
                                                    name="id-customer-checked{{ $customer->customer_id }}"
                                                    value="{{ $customer->customer_id }}"
                                                    data-customer_id="{{ $customer->customer_id }}">
                                            </label>
                                        </div>
                                    </td>
                                    <td>{{ $customer->customer_name }}</td>
                                    <td>{{ $customer->customer_email }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('.id-customer-checked').click(function() {
            var customer_id = $(this).data('customer_id');
            var id_checked = $('input[type="checkbox"][name="id-customer-checked' + customer_id + '"]:checked')
                .val();
            if (customer_id != id_checked) {
                id_checked = '';
            }
            $.ajax({
                url: '{{ url('admin/customer/selected-email') }}',
                method: 'GET',
                data: {
                    customer_id: customer_id,
                    id_checked: id_checked,
                },
                success: function(data) {
                    load_list_email();
                    if (data == 'selected') {
                        message_toastr("success", "Đã Chọn Thành Công");
                    } else if (data == 'unselected') {
                        message_toastr("success", "Đã Bỏ Thành Công");
                    }
                },
                error: function(data) {
                    alert("Nhân Ơi Fix Bug Huhu :<");
                },
            });
        });
        $('.btn-submit-email').click(function() {
            $(".loading").css({
                "display": "block"
            });
            $(".overlay-loading").css({
                "display": "block"
            });

            var to_email = $('.to_email').val();
            var title_email = $('.title_email').val();
            var content_email = $('.content_email').val();
            var _token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '{{ url('admin/customer/send-email') }}',
                method: 'POST',
                data: {
                    to_email: to_email,
                    title_email: title_email,
                    content_email: content_email,
                    _token: _token,
                },
                success: function(data) {
                    $(".loading").css({
                        "display": "none"
                    });
                    $(".overlay-loading").css({
                        "display": "none"
                    });
                    if (data == 'true') {
                        message_toastr("success", "Gửi Email Đến Khách Hàng Thành Công!");
                        load_list_email();
                        document.getElementById('titleemail').value = '';
                    }
                },
                error: function(data) {
                    $(".loading").css({
                        "display": "none"
                    });
                    $(".overlay-loading").css({
                        "display": "none"
                    });
                    alert("Nhân Ơi Fix Bug Huhu :<");
                },
            });
        });
        load_list_email();

        function load_list_email() {
            $.ajax({
                url: '{{ url('admin/customer/load-list-mail') }}',
                method: 'GET',
                data: {},
                success: function(data) {
                    document.getElementById('listmailcustomer').value = '' + data + '';
                },
                error: function(data) {
                    alert("Nhân Ơi Fix Bug Huhu :<");
                },
            });
        }
    </script>

    <script>
         ClassicEditor
            .create(document.querySelector('#editor1'))
            .then(editor => {
                console.log(editor);
            })
            .catch(error => {
                console.error(error);
            });
    </script>
@endsection
