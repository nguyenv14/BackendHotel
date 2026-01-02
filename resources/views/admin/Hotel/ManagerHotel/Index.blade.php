@extends('admin.Hotel.ManagerHotel.manager_hotel_layout')
@section('manager_hotel')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-home"></i>
                </span> Dashboard
            </h3>
        </div>
        <div class="row">
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-danger card-img-holder text-white">
                    <div class="card-body">
                        {{-- <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" /> --}}
                        <h4 class="font-weight-normal mb-3">Weekly Sales <i
                                class="mdi mdi-chart-line mdi-24px float-right"></i>
                        </h4>
                        <h2 class="mb-5">$ 15,0000</h2>
                        <h6 class="card-text">Increased by 60%</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-info card-img-holder text-white">
                    <div class="card-body">
                        {{-- <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" /> --}}
                        <h4 class="font-weight-normal mb-3">Weekly Orders <i
                                class="mdi mdi-bookmark-outline mdi-24px float-right"></i>
                        </h4>
                        <h2 class="mb-5">45,6334</h2>
                        <h6 class="card-text">Decreased by 10%</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card bg-gradient-success card-img-holder text-white">
                    <div class="card-body">
                        {{-- <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" /> --}}
                        <h4 class="font-weight-normal mb-3">Visitors Online <i
                                class="mdi mdi-diamond mdi-24px float-right"></i>
                        </h4>
                        <h2 class="mb-5">95,5741</h2>
                        <h6 class="card-text">Increased by 5%</h6>
                    </div>
                </div>
            </div>
        </div>
        <!-- Filter Chung Cho Tất Cả Biểu Đồ -->

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
                                <select id="manager_filter_type" class="form-control form-control-sm"
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
                                <input type="date" id="manager_filter_date" class="form-control form-control-sm"
                                    value="{{ date('Y-m-d') }}" style="font-size: 0.875rem; min-width: 160px;">
                            </div>

                            <!-- Apply Button -->
                            <button type="button" class="btn btn-sm btn-gradient-primary px-3" onclick="loadManagerCharts()">
                                <i class="mdi mdi-filter-variant me-1"></i>
                            </button>

                            <!-- Reset Button -->
                            <button type="button" class="btn btn-sm btn-outline-secondary px-3" onclick="resetManagerFilter()">
                                <i class="mdi mdi-refresh me-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2 Biểu Đồ: Cột và Tròn -->
        <div class="row">
            <!-- Biểu Đồ Cột - Doanh Thu Theo Tháng -->
            <div class="col-md-7 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="clearfix">
                            <h4 class="card-title float-left">Doanh Thu Theo Tháng</h4>
                        </div>
                        <div class="col-md-12" id="chart_revenue_by_month" style="height: 350px"></div>
                    </div>
                </div>
            </div>
            <!-- Biểu Đồ Tròn - Top User Đặt Phòng -->
            <div class="col-md-5 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="clearfix">
                            <h4 class="card-title float-left">Top User Đặt Phòng</h4>
                        </div>
                        <div class="col-md-12" style="height: 350px; position: relative;">
                            <canvas id="chart_top_customers_pie"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    <script>
        // Biến global để lưu chart instances
        var chart_revenue_by_month = null;
        var chart_top_customers_pie = null;
        var hotel_id = {{ $hotel->hotel_id ?? 'null' }};

        $(document).ready(function() {
            // Khởi tạo biểu đồ cột - Doanh thu theo tháng
            if ($('#chart_revenue_by_month').length > 0 && hotel_id) {
                chart_revenue_by_month = new Morris.Bar({
                    element: 'chart_revenue_by_month',
                    barColors: ['#33CCFF'],
                    hideHover: 'auto',
                    parseTime: false,
                    data: [],
                    xkey: 'date',
                    ykeys: ['revenue'],
                    labels: ['Doanh Thu'],
                    resize: true
                });
            }

            // Load tất cả biểu đồ khi trang load
            loadManagerCharts();
        });

        // Hàm load tất cả biểu đồ với filter chung
        function loadManagerCharts() {
            if (!hotel_id) {
                console.log("Không có hotel_id");
                return;
            }

            var filter_type = $('#manager_filter_type').val();
            var date = $('#manager_filter_date').val();

            // Load biểu đồ doanh thu theo tháng
            loadRevenueByMonth(filter_type, date);

            // Load biểu đồ top customers
            loadTopCustomers(filter_type, date);
        }

        // Hàm load biểu đồ doanh thu theo tháng
        function loadRevenueByMonth(filter_type, date) {
            if (!hotel_id) return;

            $.ajax({
                url: '{{ url('/admin/hotel/manager/dashboard/revenue-by-month') }}',
                method: 'GET',
                dataType: 'JSON',
                data: {
                    hotel_id: hotel_id,
                    filter_type: filter_type,
                    date: date
                },
                success: function(data) {
                    if (data && data.length > 0) {
                        chart_revenue_by_month.setData(data);
                    } else {
                        chart_revenue_by_month.setData([]);
                        console.log("Không có dữ liệu doanh thu");
                    }
                },
                error: function(xhr, status, error) {
                    console.log("Lỗi khi tải biểu đồ doanh thu:", error);
                    if (chart_revenue_by_month) {
                        chart_revenue_by_month.setData([]);
                    }
                }
            });
        }

        // Hàm load biểu đồ top customers (Pie Chart)
        function loadTopCustomers(filter_type, date) {
            if (!hotel_id) return;

            $.ajax({
                url: '{{ url('/admin/hotel/manager/dashboard/top-customers') }}',
                method: 'GET',
                dataType: 'JSON',
                data: {
                    hotel_id: hotel_id,
                    filter_type: filter_type,
                    date: date
                },
                success: function(data) {
                    var customers = data;

                    // Chuẩn bị dữ liệu cho pie chart
                    var chartData = customers.map(function(customer) {
                        return {
                            label: customer.customer_name.length > 20 ? customer.customer_name.substring(0, 20) + '...' : customer.customer_name,
                            value: customer.total_orders
                        };
                    });

                    // Sử dụng Chart.js cho pie chart
                    var ctx = document.getElementById('chart_top_customers_pie');
                    if (ctx) {
                        if (chart_top_customers_pie) {
                            chart_top_customers_pie.destroy();
                        }
                        chart_top_customers_pie = new Chart(ctx.getContext('2d'), {
                            type: 'pie',
                            data: {
                                labels: chartData.map(function(item) {
                                    return item.label;
                                }),
                                datasets: [{
                                    data: chartData.map(function(item) {
                                        return item.value;
                                    }),
                                    backgroundColor: ['#FF3399', '#A4ADD3', '#33CCFF', '#fc8710', '#FF6541', '#FF9966', '#00CC99', '#CC99FF', '#FFCC99', '#99CCFF']
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
                                            return data.labels[tooltipItem.index] + ': ' + data.datasets[0].data[tooltipItem.index] + ' đơn';
                                        }
                                    }
                                }
                            }
                        });
                    }
                },
                error: function() {
                    console.log("Lỗi khi tải dữ liệu top customers");
                }
            });
        }

        // Hàm đặt lại filter về mặc định
        function resetManagerFilter() {
            $('#manager_filter_type').val('month');
            $('#manager_filter_date').val('{{ date('Y-m-d') }}');
            loadManagerCharts();
        }
    </script>
@endsection
