@extends('admin.admin_layout')
@section('admin_content')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-home"></i>
            </span>Dashboard
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="mdi mdi-timetable"></i>
                    <span id="time"></span>
                    <span><?php
                    $today = date('d/m/Y');
                    echo $today;
                    ?></span>
                </li>
            </ul>
        </nav>
    </div>
    <div class="row">
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-left">
                            <i class="mdi mdi-cube text-danger icon-lg"></i>
                        </div>
                        <div class="float-right">
                            <p class="mb-0 text-right">Doanh Thu Hôm Nay</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0">{{ number_format($todays_revenue,0,',','.') }}đ</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-left">
                            <i class="mdi mdi-cube text-danger icon-lg"></i>
                        </div>
                        <div class="float-right">
                            <p class="mb-0 text-right">Đơn Hàng Hôm Nay</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0">{{ $todays_order }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-left">
                            <i class="mdi mdi-account-star text-success icon-lg"></i>
                        </div>
                        <div class="float-right">
                            <p class="mb-0 text-right">Đánh Giá Hôm Nay</p>
                            <div id="count_admin_online" class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0">{{ $evaluate_order }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-left">
                            <i class="mdi mdi-account-box-multiple text-info icon-lg"></i>
                        </div>
                        <div class="float-right">
                            <p class="mb-0 text-right">Khách Hàng Online</p>
                            <div id="count_customer_online" class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0">{{ $count_customer_online }}</h3>   
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 stretch-card grid-margin">
            <div class="card bg-gradient-danger card-img-holder text-white">
                <div class="card-body">

                    <img src="{{ asset('public/backend/assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                        alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Tổng Đơn Hàng <i
                            class="mdi mdi-chart-line mdi-24px float-right"></i>
                    </h4>
                    <h2 class="mb-5">{{ $count_order }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 stretch-card grid-margin">
            <div class="card bg-gradient-info card-img-holder text-white">
                <div class="card-body">
                    <img src="{{ asset('public/backend/assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                        alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Tổng Admin <i
                            class="mdi mdi-bookmark-outline mdi-24px float-right"></i>
                    </h4>
                    <h2 class="mb-5">{{ $count_admin }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 stretch-card grid-margin">
            <div class="card bg-gradient-success card-img-holder text-white">
                <div class="card-body">
                    <img src="{{ asset('public/backend/assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                        alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Tổng Khách Hàng <i class="mdi mdi-diamond mdi-24px float-right"></i>
                    </h4>
                    <h2 class="mb-5">{{ $count_customer }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="clearfix">
                        <h4 class="card-title float-left">Thống Kê Doanh Số</h4>
                        <div class="col-md-12" id="chart_statistical" style="height: 300px">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
          <div class="card">
              <div class="card-body">
                  <div class="clearfix">
                      <h4 class="card-title float-left">Khách Truy Cập và Số Lần Xem Trang</h4>
                      <div class="col-md-12" id="chart_visitors" style="height: 300px">

                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
    <div class="row">
        {{-- <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Top Trình Duyệt Truy Cập (GG Analytics API)</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th> Top </th>
                                    <th> Tên Trình Duyệt </th>
                                    <th> Số Phiên </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($top_browser as $key => $v_top_browser)
                                    <tr>
                                        <td> {{ $key + 1 }} </td>
                                        <td> {{ $v_top_browser['browser'] }} </td>
                                        <td> {{ $v_top_browser['sessions'] }} </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> --}}
        {{-- <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Top 10 Trang Truy Cập Nhiều Nhất Trong Tháng (GG Analytics API)</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th> Top </th>
                                    <th> Tên Trang </th>
                                    <th> Tổng Lượt Xem </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pages_one_day as $key => $v_pages_one_day)
                                    <tr>
                                        <td> {{ $key + 1 }} </td>
                                        <td> {{ $v_pages_one_day['pageTitle'] }} </td>
                                        <td> {{ $v_pages_one_day['pageViews'] }} </td>
                                    </tr>
                                    @if ($key == 9)
                                        @php
                                            break;
                                        @endphp
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- <script>
        $(document).ready(function() {
          
           var chart_statistical =  new Morris.Bar({
                // ID of the element in which to draw the chart.
                element: 'chart_statistical',
                lineColors: ['#33CCFF', '#fc8710', '#FF6541', '#A4ADD3', '#FF3399'],
                // Chart data records -- each entry in this array corresponds to a point on
                // the chart.
                pointFillColors:['#ffffff'],
                pointStrokeColors:['black'],
                fillOpacity:0.6,
                hideHover:'auto',
                parseTime: false,
                data: [
                ],
                // The name of the data record attribute that contains x-values.
                xkey: 'order_date',
                // A list of names of data record attributes that contain y-values.
                ykeys: ['sales' , 'order_refused' , 'price_order_refused' , 'quantity_order_room' , 'total_order'],
                // Labels for the ykeys -- will be displayed when you hover over the
                // chart.
                labels: ['Doanh Thu' , 'Đơn Hủy' , 'Tiền Đơn Hủy' , ' Số Phòng' , 'Tổng Đơn Hàng']
            });


            $.ajax({
                url: '{{ url('/admin/dashboard/chart-statistical') }}',
                method: 'GET',
                dateType: 'JSON',
                data: {

                },
                success: function(data) {
                    chart_statistical.setData(JSON.parse(data));
                },
                error: function() {
                    alert("Bug Ở Biểu Đồ Doanh Số :<<");
                }
            })

        // function chart_statistical() {
        // }
        // chart_statistical();

        });
    </script> --}}

{{-- <script>
  $(document).ready(function() {
     var chart_visitors =  new Morris.Area({
          // ID of the element in which to draw the chart.
          element: 'chart_visitors',
          lineColors: ['#FF9966', '#fc8710', '#FF6541'],
          // Chart data records -- each entry in this array corresponds to a point on
          // the chart.
          pointFillColors:['#ffffff'],
          pointStrokeColors:['black'],
          fillOpacity:0.6,
          hideHover:'auto',
          parseTime: false,
          data: [

          ],
          // The name of the data record attribute that contains x-values.
          xkey: 'date',
          // A list of names of data record attributes that contain y-values.
          ykeys: ['pageViews' , 'visitors'],
          // Labels for the ykeys -- will be displayed when you hover over the
          // chart.
          labels: ['Số Lượt Xem' , 'Khách Ghé Thăm']
      });


      $.ajax({
          url: '{{ url('/admin/dashboard/chart-visitors') }}',
          method: 'GET',
          dateType: 'JSON',
          data: {

          },
          success: function(data) {
            chart_visitors.setData(JSON.parse(data));
            },
            error: function() {
                alert("Bug Ở Biểu Đồ Khách Hàng Ghé Xem :<<");
            }
        })

  // function chart_statistical() {
  // }
  // chart_statistical();

  });
</script> --}}




    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
@endsection
