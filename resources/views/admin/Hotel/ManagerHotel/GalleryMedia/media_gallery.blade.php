@extends('admin.Hotel.ManagerHotel.manager_hotel_layout')
@section('manager_hotel')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-crosshairs-gps"></i>
            </span> Quản Lý Khách Sạn
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
                <h4 class="card-title">Thư Viện Ảnh Khách Sạn {{ $hotel->hotel_name }}</h4>
                <div class="row mt-3">
                    @php
                        $hotel_name = $hotel->hotel_name;
                        $folder = preg_replace('/\s+/', '', $hotel_name);
                    @endphp
                    @foreach ($images as $image)
                        <div class="col-6 pe-1">
                            <img height="366px" style="object-fit: cover" src="{{ asset('public/fontend/assets/img/hotel/gallery_' . $folder . '/' . $image->gallery_hotel_image) }}"
                                class="mb-2 mw-100 w-100 rounded" alt="{{  $image->gallery_hotel_content }}">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Thư Viện Video Khách Sạn {{ $hotel->hotel_name }}</h4>
                <div class="row mt-3">
                    @php
                        $hotel_name = $hotel->hotel_name;
                        $folder = preg_replace('/\s+/', '', $hotel_name);
                    @endphp
                    @foreach ($video as $video)
                        <div class="col-6 pe-1">
                            <video width="1110px" type="video/mp4" autoplay="" muted="" loop="">
                                <source src="{{ asset('public/fontend/assets/img/hotel/gallery_' . $folder . '/' . $video->gallery_hotel_image) }}" />
                            </video>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
@endsection
