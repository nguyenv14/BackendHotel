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

     <!-- Thống Kê Tổng Quan -->
     <div class="row">
        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 grid-margin stretch-card">
            <div class="card card-statistics">
                <div class="card-body">
                    <div class="clearfix">
                        <div class="float-left">
                            <i class="mdi mdi-cube text-danger icon-lg"></i>
                        </div>
                        <div class="float-right">
                            <p class="mb-0 text-right">Doanh Thu Hôm Nay</p>
                            <div class="fluid-container">
                                <h3 class="font-weight-medium text-right mb-0">
                                    {{ number_format($todays_revenue, 0, ',', '.') }}đ</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 grid-margin stretch-card">
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
        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 grid-margin stretch-card">
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
        {{-- <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
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
        </div> --}}

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
                    <h4 class="font-weight-normal mb-3">Tổng Khách Hàng <i
                            class="mdi mdi-diamond mdi-24px float-right"></i>
                    </h4>
                    <h2 class="mb-5">{{ $count_customer }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Chung Cho Tất Cả Biểu Đồ -->
    <!-- Bộ Lọc Thống Kê - Đã được thiết kế lại -->
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-2" style="flex-wrap: nowrap;">
                        <h5 class="mb-0 text-muted fw-medium me-2" style="white-space: nowrap;">Bộ Lọc Thống Kê:</h5>

                        <!-- Filter Type -->
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-gradient-primary text-white d-flex align-items-center"
                                style="padding: 0.375rem 0.75rem;">
                                <i class="mdi mdi-filter me-1"></i>
                            </span>
                            <select id="common_filter_type" class="form-control form-control-sm"
                                style="font-size: 0.875rem; min-width: 120px;">
                                <option value="day">Theo Ngày</option>
                                <option value="month" selected>Theo Tháng</option>
                                <option value="year">Theo Năm</option>
                            </select>
                        </div>

                        <!-- Date Picker -->
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-gradient-primary text-white d-flex align-items-center"
                                style="padding: 0.375rem 0.75rem;">
                                <i class="mdi mdi-calendar me-1"></i>
                            </span>
                            <input type="date" id="common_filter_date" class="form-control form-control-sm"
                                value="{{ date('Y-m-d') }}" style="font-size: 0.875rem; min-width: 160px;">
                        </div>

                        <!-- Apply Button -->
                        <button type="button" class="btn btn-sm btn-gradient-primary px-3" onclick="loadAllCharts()" style="flex-shrink: 0;">
                            <i class="mdi mdi-filter-variant me-1"></i>
                        </button>

                        <!-- Reset Button -->
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3" onclick="resetFilter()" style="flex-shrink: 0;">
                            <i class="mdi mdi-refresh me-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu Đồ Cột - Thống Kê Doanh Số -->
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="clearfix">
                        <h4 class="card-title float-left">Thống Kê Doanh Số</h4>
                    </div>
                    <div class="col-md-12" id="chart_statistical" style="height: 350px"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2 Biểu Đồ Tròn -->
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="clearfix">
                        <h4 class="card-title float-left">Top Khách Sạn Có Đơn Đặt Cao</h4>
                    </div>
                    <div class="col-md-12" style="height: 350px; position: relative;">
                        <canvas id="chart_top_hotels_donut"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="clearfix">
                        <h4 class="card-title float-left">Top Người Đặt Phòng</h4>
                    </div>
                    <div class="col-md-12" style="height: 350px; position: relative;">
                        <canvas id="chart_top_customers_donut"></canvas>
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
         <style>
             /* Enhance input group styling */
            .input-group-text.bg-gradient-primary {
                border-radius: 0.25rem 0 0 0.25rem;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .form-control.form-control-sm {
                border-radius: 0 0.25rem 0.25rem 0; 
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                transition: border-color 0.2s ease;
            }

            .form-control.form-control-sm:focus {
                border-color: #9c27b0;
                box-shadow: 0 0 0 0.2rem rgba(156, 39, 176, 0.25);
            }

            /* Card body padding adjustment */
            .card-body.p-3 {
                padding: 1rem !important;
            }

            /* Responsive adjustments */
            @media (max-width: 768px) {
                .d-flex.align-items-center.gap-3.flex-wrap {
                    flex-direction: column;
                    align-items: stretch;
                }

                .input-group {
                    width: 100%;
                }

                .btn-sm {
                    width: 100%;
                    margin-top: 0.5rem;
                }
            }
        </style>
        <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
        <script>
            // Biến global để lưu chart instances
            var chart_statistical = null;
            var chart_top_hotels_donut = null;
            var chart_top_customers_donut = null;

            $(document).ready(function() {

                // Kiểm tra element có tồn tại không
                if ($('#chart_statistical').length > 0) {
                    chart_statistical = new Morris.Bar({
                        // ID of the element in which to draw the chart.
                        element: 'chart_statistical',
                        barColors: ['#33CCFF', '#fc8710', '#FF6541', '#A4ADD3', '#FF3399'],
                        // Chart data records -- each entry in this array corresponds to a point on
                        // the chart.
                        pointFillColors: ['#ffffff'],
                        pointStrokeColors: ['black'],
                        fillOpacity: 0.6,
                        hideHover: 'auto',
                        parseTime: false,
                        data: [],
                        // The name of the data record attribute that contains x-values.
                        xkey: 'order_date',
                        // A list of names of data record attributes that contain y-values.
                        ykeys: ['sales', 'order_refused', 'price_order_refused', 'quantity_order_room',
                            'total_order'
                        ],
                        // Labels for the ykeys -- will be displayed when you hover over the
                        // chart.
                        labels: ['Doanh Thu', 'Đơn Hủy', 'Tiền Đơn Hủy', ' Số Phòng', 'Tổng Đơn Hàng'],
                        resize: true
                    });
                }


                // Load biểu đồ thống kê doanh số
                loadChartStatistical();

                // Load tất cả biểu đồ khi trang load
                loadAllCharts();
            });

            // Hàm load biểu đồ thống kê doanh số
            function loadChartStatistical() {
                var filter_type = $('#common_filter_type').val();
                var date = $('#common_filter_date').val();

                $.ajax({
                    url: '{{ url('/admin/dashboard/chart-statistical') }}',
                    method: 'GET',
                    dataType: 'JSON',
                    data: {
                        filter_type: filter_type,
                        date: date
                    },
                    success: function(data) {
                        // data đã được jQuery parse thành object rồi
                        if (data && data.length > 0) {
                            chart_statistical.setData(data);
                        } else {
                            // Nếu không có dữ liệu, set mảng rỗng
                            chart_statistical.setData([]);
                            console.log("Không có dữ liệu thống kê");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("Lỗi khi tải biểu đồ thống kê doanh số:", error);
                        console.log("Response:", xhr.responseText);
                        // Set dữ liệu rỗng để tránh lỗi
                        chart_statistical.setData([]);
                    }
                });
            }

            // Hàm load tất cả biểu đồ với filter chung
            function loadAllCharts() {
                var filter_type = $('#common_filter_type').val();
                var date = $('#common_filter_date').val();

                // Load biểu đồ thống kê doanh số
                loadChartStatistical();

                // Load top khách sạn
                loadTopHotels(filter_type, date);

                // Load top khách hàng
                loadTopCustomers(filter_type, date);
            }

            // Hàm load top khách sạn (Donut Chart)
            function loadTopHotels(filter_type, date) {
                if (!filter_type) filter_type = $('#common_filter_type').val();
                if (!date) date = $('#common_filter_date').val();

                $.ajax({
                    url: '{{ url('/admin/dashboard/top-hotels-by-orders') }}',
                    method: 'GET',
                    dataType: 'JSON',
                    data: {
                        filter_type: filter_type,
                        date: date
                    },
                    success: function(data) {
                        // data đã được jQuery parse thành object rồi
                        var hotels = data;

                        // Chuẩn bị dữ liệu cho pie chart
                        var chartData = hotels.map(function(hotel) {
                            return {
                                label: hotel.hotel_name.length > 25 ? hotel.hotel_name.substring(0, 25) +
                                    '...' : hotel.hotel_name,
                                value: hotel.total_orders
                            };
                        });

                        // Sử dụng Chart.js cho pie chart
                        var ctx = document.getElementById('chart_top_hotels_donut');
                        if (ctx) {
                            if (chart_top_hotels_donut) {
                                chart_top_hotels_donut.destroy();
                            }
                            chart_top_hotels_donut = new Chart(ctx.getContext('2d'), {
                                type: 'pie',
                                data: {
                                    labels: chartData.map(function(item) {
                                        return item.label;
                                    }),
                                    datasets: [{
                                        data: chartData.map(function(item) {
                                            return item.value;
                                        }),
                                        backgroundColor: ['#33CCFF', '#fc8710', '#FF6541',
                                            '#A4ADD3', '#FF3399', '#FF9966', '#00CC99',
                                            '#CC99FF', '#FFCC99', '#99CCFF'
                                        ]
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    legend: {
                                        position: 'right'
                                    },
                                    tooltips: {
                                        callbacks: {
                                            label: function(tooltipItem, data) {
                                                return data.labels[tooltipItem.index] + ': ' + data
                                                    .datasets[0].data[tooltipItem.index] + ' đơn';
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    },
                    error: function() {
                        console.log("Lỗi khi tải dữ liệu top khách sạn");
                    }
                });
            }

            // Hàm load top khách hàng (Pie Chart)
            function loadTopCustomers(filter_type, date) {
                if (!filter_type) filter_type = $('#common_filter_type').val();
                if (!date) date = $('#common_filter_date').val();

                $.ajax({
                    url: '{{ url('/admin/dashboard/top-customers-by-orders') }}',
                    method: 'GET',
                    dataType: 'JSON',
                    data: {
                        filter_type: filter_type,
                        date: date
                    },
                    success: function(data) {
                        // data đã được jQuery parse thành object rồi
                        var customers = data;

                        // Chuẩn bị dữ liệu cho pie chart
                        var chartData = customers.map(function(customer) {
                            return {
                                label: customer.customer_name.length > 25 ? customer.customer_name
                                    .substring(0, 25) + '...' : customer.customer_name,
                                value: customer.total_orders
                            };
                        });

                        // Sử dụng Chart.js cho pie chart
                        var ctx = document.getElementById('chart_top_customers_donut');
                        if (ctx) {
                            if (chart_top_customers_donut) {
                                chart_top_customers_donut.destroy();
                            }
                            chart_top_customers_donut = new Chart(ctx.getContext('2d'), {
                                type: 'pie',
                                data: {
                                    labels: chartData.map(function(item) {
                                        return item.label;
                                    }),
                                    datasets: [{
                                        data: chartData.map(function(item) {
                                            return item.value;
                                        }),
                                        backgroundColor: ['#FF3399', '#A4ADD3', '#33CCFF',
                                            '#fc8710', '#FF6541', '#FF9966', '#00CC99',
                                            '#CC99FF', '#FFCC99', '#99CCFF'
                                        ]
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    legend: {
                                        position: 'right'
                                    },
                                    tooltips: {
                                        callbacks: {
                                            label: function(tooltipItem, data) {
                                                return data.labels[tooltipItem.index] + ': ' + data
                                                    .datasets[0].data[tooltipItem.index] + ' đơn';
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    },
                    error: function() {
                        console.log("Lỗi khi tải dữ liệu top khách hàng");
                    }
                });
            }

            // Hàm format tiền tệ
            function formatCurrency(amount) {
                return new Intl.NumberFormat('vi-VN').format(Math.round(amount));
            }

            // Hàm đặt lại filter về mặc định
            function resetFilter() {
                $('#common_filter_type').val('month');
                $('#common_filter_date').val('{{ date('Y-m-d') }}');
                loadAllCharts();
            }
        </script>
    @endsection
