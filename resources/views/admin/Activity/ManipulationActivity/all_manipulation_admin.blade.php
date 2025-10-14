@extends('admin.admin_layout')
@section('admin_content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-crosshairs-gps"></i>
            </span> Quản Lý Hoạt Động
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
                    <div class="card-title col-sm-9">Bảng Danh Sách Hoạt Động Admin</div>
                    <div class="col-sm-3">
                        <form action="{{ URL::to('') }}" method="get">
                            <div class="input-group">
                                <input type="text" class="form-control" name="" placeholder="Search">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-gradient-primary me-2">Tìm kiếm</button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
                <table style="margin-top:20px " class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tên Admin</th>
                            <th>Hành Động</th>
                            <th>Thời Gian</th>
                        </tr>
                    </thead>
                    <tbody>
                       @foreach ($admin_manipulation as $v_admin_manipulation)
                       <tr>
                           <td>{{$v_admin_manipulation->manipulation_activity_admin_name}}</td>
                           <td>{{$v_admin_manipulation->manipulation_activity_action}}</td>
                           <td>{{$v_admin_manipulation->created_at}}</td>
                       </tr>   
                       @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>
    <nav>
        {!! $admin_manipulation->links() !!}
    </nav>
@endsection
