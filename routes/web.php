<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerADSController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CheckOutController;
use App\Http\Controllers\CompanyConfigController;
use App\Http\Controllers\ConfigWebController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacilitiesHotelController;
use App\Http\Controllers\FacilitiesRoomController;
use App\Http\Controllers\GalleryHotelController;
use App\Http\Controllers\GalleryRoomController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\LoginAndRegister;
use App\Http\Controllers\ManagerHotelController;
use App\Http\Controllers\ManipulationActivityController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ServiceChargeController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\TypeRoomController;
use App\Http\Controllers\SearchMasterController;
use App\Http\Controllers\CheckOrderCustomerController;
use App\Http\Controllers\EvaluateController;
use App\Http\Controllers\RestaurantsController;

use Illuminate\Support\Facades\Route;

/* Back-end */
/* Admin Auth Login */
Route::controller(AuthController::class)->group(function () {
    Route::group(['prefix' => 'admin/auth'], function () {
        Route::GET('/register', 'show_register')->middleware('admin.roles');
        Route::POST('/registration-processing', 'registration_processing')->middleware('admin.roles');
        Route::GET('/login', 'show_login');
        Route::POST('/login-processing', 'login_processing');
        Route::GET('/logout', 'logout');
    });
});
/* Bảo Vệ Toàn Bộ URL Tránh Bị Vượt */
Route::group(['middleware' => 'ProtectAuthLogin'], function () {

    Route::controller(DashboardController::class)->group(function () {
        Route::GET('admin', 'show_dashboard');
        Route::group(['prefix' => 'admin/dashboard', 'middleware' => 'hotel.manager.access'], function () {
            Route::GET('/', 'show_dashboard');
            Route::GET('/dashboard', 'show_dashboard');
            Route::GET('/chart-statistical', [DashboardController::class, 'chart_statistical']);
            Route::GET('/chart-visitors', [DashboardController::class, 'chart_visitors']);
            Route::GET('/statistical', [DashboardController::class, 'statistical']);
        });
    });

    /* Quản Lý Admin */
    Route::controller(AdminController::class)->group(function () {
        Route::group(['prefix' => 'admin/auth', 'middleware' => 'hotel.manager.access'], function () {
            Route::group(['middleware' => 'admin.roles'], function () {
                Route::GET('/load-table-admin', 'loading_table_admin');
                Route::GET('/all-admin', 'all_admin');
                Route::POST('/assign-roles', 'assign_roles');
                Route::GET('/edit-admin', 'edit_admin');
                Route::POST('/update-admin', 'update_admin');
                Route::GET('/all-admin-sreach', 'search_all_admin');
                Route::GET('/delete-admin-roles', 'delete_admin_roles');
                Route::GET('/impersonate', 'impersonate');
            });
            Route::GET('/destroy-impersonate', 'destroy_impersonate');
        });
    });

    Route::group(['prefix' => 'admin/restaurants', 'middleware' => 'hotel.manager.access'], function () {
        Route::get('/all-restaurants', [RestaurantsController::class, 'index']);
        Route::get('/add-restaurant', [RestaurantsController::class, 'add_restaurant']);
        Route::get('/edit-restaurant', [RestaurantsController::class, 'edit_restaurant']);
        Route::get('/restaurant-detail', [RestaurantsController::class, 'restaurant_detail']);
        Route::get('/load-restaurants', [RestaurantsController::class, 'load_restaurants']);
        Route::post('loading-gallery', [RestaurantsController::class, 'loading_gallery']);
        Route::post('/save-restaurant', [RestaurantsController::class, 'save_restaurant']);
        Route::post('/update-restaurant', [RestaurantsController::class, 'update_restaurant']);
        Route::post('/insert-menu', [RestaurantsController::class, 'insert_menu']);
        Route::post('/insert-table', [RestaurantsController::class, 'insert_table']);
        Route::get('/delete-item', [RestaurantsController::class, 'delete_item']);
        Route::post('/insert-gallery/{restaurant_id}', [RestaurantsController::class, 'insert_gallery']);
    });

    /* Quản Lý Khách Hàng */
    Route::controller(CustomerController::class)->group(function () {
        Route::group(['prefix' => 'admin/customer', 'middleware' => 'hotel.manager.access'], function () {
            Route::GET('/all-customer', 'all_customer');
            Route::GET('/load-all-customer', 'load_all_customer');
            Route::GET('/all-customer-sreach', 'search_all_customer');
            Route::GET('/sort-customer-by-type', 'sort_customer_bytype');
            Route::GET('/update-status-customer', 'update_status_customer');
            Route::GET('/delete-customer', 'delete_customer');
            Route::GET('/list-soft-deleted-customer', 'garbage_can');
            Route::GET('/count-bin', 'count_bin');
            Route::GET('/load-list-soft-deleted', 'load_garbage_can');
            Route::GET('/search-bin', 'search_bin');
            Route::GET('/restore-customer', 'un_trash');
            Route::GET('/delete-trash-customer', 'trash_delete');
            Route::GET('/view-email', 'view_email');
            Route::GET('/selected-email', 'selected_email');
            Route::GET('load-list-mail', 'load_list_mail');
            Route::POST('/send-email', 'send_email');
        });
    });
    /* Slider */
    Route::controller(SliderController::class)->group(function () {
        Route::group(['prefix' => 'admin/slider', 'middleware' => 'hotel.manager.access'], function () {
            Route::GET('/', 'list_items');
            Route::GET('/all-slider', 'list_items');
            Route::GET('/search-slider', 'search_items');
            Route::GET('/add-slider', 'insert_item');
            Route::POST('/save-slider', 'save_item');
            Route::GET('/load-slider', 'load_items');
            Route::GET('/edit-slider', 'edit_item');
            Route::POST('/update-slider', 'update_item');
            Route::POST('/update-status-slider', 'update_status_item');
            Route::POST('/delete-slider', 'move_to_bin');
            Route::GET('/count-bin', 'count_bin');
            Route::GET('/list-deleted-slider', 'list_bin');
            Route::GET('/load-deleted-slider', 'load_bin');
            Route::GET('/search-bin', 'search_bin');
            Route::POST('/restore-slider', 'un_bin');
            Route::POST('/delete-trash-slider', 'bin_delete');
        });
    });

    /* Quản Lý Tiện Ích Phòng */
    Route::controller(BannerADSController::class)->group(function () {
        Route::group(['prefix' => 'admin/bannerads', 'middleware' => 'hotel.manager.access'], function () {
            Route::GET('/', 'list_items');
            Route::GET('/all-banner-ads', 'list_items');
            Route::GET('/search-banner-ads', 'search_items');
            Route::GET('/add-banner-ads', 'insert_item');
            Route::POST('/save-banner-ads', 'save_item');
            Route::GET('/load-banner-ads', 'load_items');
            Route::GET('/edit-banner-ads', 'edit_item');
            Route::POST('/update-banner-ads', 'update_item');
            Route::POST('/update-status-banner-ads', 'update_status_item');
            Route::POST('/delete-banner-ads', 'move_to_bin');
            Route::GET('/count-bin', 'count_bin');
            Route::GET('/list-deleted-banner-ads', 'list_bin');
            Route::GET('/load-deleted-banner-ads', 'load_bin');
            Route::GET('/search-bin', 'search_bin');
            Route::POST('/restore-banner-ads', 'un_bin');
            Route::POST('/delete-trash-banner-ads', 'bin_delete');
        });
    });

    /* Thương Hiệu */
    Route::controller(BrandController::class)->group(function () {
        Route::group(['prefix' => 'admin/brand', 'middleware' => 'hotel.manager.access'], function () {
            Route::GET('/', 'list_items');
            Route::GET('/all-brand', 'list_items');
            Route::GET('/search-brand', 'search_items');
            Route::GET('/add-brand', 'insert_item');
            Route::POST('/save-brand', 'save_item');
            Route::GET('/load-brand', 'load_items');
            Route::GET('/edit-brand', 'edit_item');
            Route::POST('/update-brand', 'update_item');
            Route::POST('/update-status-brand', 'update_status_item');
            Route::POST('/delete-brand', 'move_to_bin');
            Route::GET('/count-bin', 'count_bin');
            Route::GET('/list-deleted-brand', 'list_bin');
            Route::GET('/load-deleted-brand', 'load_bin');
            Route::GET('/search-bin', 'search_bin');
            Route::POST('/restore-brand', 'un_bin');
            Route::POST('/delete-trash-brand', 'bin_delete');
        });
    });

    /* Khu Vực */
    Route::controller(AreaController::class)->group(function () {
        Route::group(['prefix' => 'admin/area', 'middleware' => 'hotel.manager.access'], function () {
            Route::GET('/', 'list_items');
            Route::GET('/all-area', 'list_items');
            Route::GET('/search-area', 'search_items');
            Route::GET('/add-area', 'insert_item');
            Route::POST('/save-area', 'save_item');
            Route::GET('/load-area', 'load_items');
            Route::GET('/edit-area', 'edit_item');
            Route::POST('/update-area', 'update_item');
            Route::POST('/update-status-area', 'update_status_item');
            Route::POST('/delete-area', 'move_to_bin');
            Route::GET('/count-bin', 'count_bin');
            Route::GET('/list-deleted-area', 'list_bin');
            Route::GET('/load-deleted-area', 'load_bin');
            Route::GET('/search-bin', 'search_bin');
            Route::POST('/restore-area', 'un_bin');
            Route::POST('/delete-trash-area', 'bin_delete');
        });
    });

    /* Khách Sạn */
    Route::controller(ManagerHotelController::class)->group(function () {
        Route::group(['prefix' => 'admin/hotel', 'middleware' => 'hotel.manager.access'], function () {
            Route::GET('/', 'list_items');
            Route::GET('/all-hotel', 'list_items');
            Route::GET('/search-hotel', 'search_items');
            Route::GET('/add-hotel', 'insert_item');
            Route::POST('/save-hotel', 'save_item');
            Route::GET('/load-hotel', 'load_items');

            Route::POST('/update-status-hotel', 'update_status_item');
            Route::POST('/delete-hotel', 'move_to_bin');
            Route::GET('/count-bin', 'count_bin');
            Route::GET('/list-deleted-hotel', 'list_bin');
            Route::GET('/load-deleted-hotel', 'load_bin');
            Route::GET('/search-bin', 'search_bin');
            Route::POST('/restore-hotel', 'un_bin');
            Route::POST('/delete-trash-hotel', 'bin_delete');

            Route::group(['prefix' => '/manager'], function () {
                Route::GET('/', 'index');
                Route::GET('/edit-hotel', 'edit_item')->name('edit-hotel');
                Route::POST('/update-hotel', 'update_item');
            });
        });
    }); 

    /* Quản Lý Tiện Ích Khách Sạn */
    Route::controller(FacilitiesHotelController::class)->group(function () {
        Route::group(['prefix' => 'admin/hotel/facilities/hotel'], function () {
            Route::GET('/', 'list_items');
            Route::GET('/all-hotel-facilities', 'list_items');
            Route::GET('/search-hotel-facilities', 'search_items');
            Route::GET('/add-hotel-facilities', 'insert_item');
            Route::POST('/save-hotel-facilities', 'save_item');
            Route::GET('/load-hotel-facilities', 'load_items');
            Route::GET('/edit-hotel-facilities', 'edit_item');
            Route::POST('/update-hotel-facilities', 'update_item');
            Route::POST('/update-status-hotel-facilities', 'update_status_item');
            Route::POST('/delete-hotel-facilities', 'move_to_bin');
            Route::GET('/count-bin', 'count_bin');
            Route::GET('/list-deleted-hotel-facilities', 'list_bin');
            Route::GET('/load-deleted-hotel-facilities', 'load_bin');
            Route::GET('/search-bin', 'search_bin');
            Route::POST('/restore-hotel-facilities', 'un_bin');
            Route::POST('/delete-trash-hotel-facilities', 'bin_delete');
        });
    });

    /* Quản Lý Tiện Ích Phòng */
    Route::controller(FacilitiesRoomController::class)->group(function () {
        Route::group(['prefix' => 'admin/hotel/facilities/room'], function () {
            Route::GET('/', 'list_items');
            Route::GET('/all-room-facilities', 'list_items');
            Route::GET('/search-room-facilities', 'search_items');
            Route::GET('/add-room-facilities', 'insert_item');
            Route::POST('/save-room-facilities', 'save_item');
            Route::GET('/load-room-facilities', 'load_items');
            Route::GET('/edit-room-facilities', 'edit_item');
            Route::POST('/update-room-facilities', 'update_item');
            Route::POST('/update-status-room-facilities', 'update_status_item');
            Route::POST('/delete-room-facilities', 'move_to_bin');
            Route::GET('/count-bin', 'count_bin');
            Route::GET('/list-deleted-room-facilities', 'list_bin');
            Route::GET('/load-deleted-room-facilities', 'load_bin');
            Route::GET('/search-bin', 'search_bin');
            Route::POST('/restore-room-facilities', 'un_bin');
            Route::POST('/delete-trash-room-facilities', 'bin_delete');
        });
    });

    /* Quản Lý Phí Dịch Vụ Khách Sạn */
    Route::controller(ServiceChargeController::class)->group(function () {
        Route::group(['prefix' => 'admin/servicecharge'], function () {
            Route::GET('/', 'list_items');
            Route::GET('/all-service-charge', 'list_items');
            Route::GET('/search-service-charge', 'search_items');
            Route::GET('/add-service-charge', 'insert_item');
            Route::POST('/save-service-charge', 'save_item');
            Route::GET('/load-service-charge', 'load_items');
            Route::GET('/edit-service-charge', 'edit_item');
            Route::POST('/update-service-charge', 'update_item');
            Route::POST('/delete-service-charge', 'move_to_bin');
            Route::GET('/count-bin', 'count_bin');
            Route::GET('/list-deleted-service-charge', 'list_bin');
            Route::GET('/load-deleted-service-charge', 'load_bin');
            Route::GET('/search-bin', 'search_bin');
            Route::POST('/restore-service-charge', 'un_bin');
            Route::POST('/delete-trash-service-charge', 'bin_delete');
        });
    });

    /* Gallery - Thư Viện Ảnh - Video Khách Sạn */
    Route::controller(GalleryHotelController::class)->group(function () {
        Route::group(['prefix' => 'admin/hotel/manager'], function () {
            Route::GET('/list-media-gallery', 'list_media');
            Route::GET('/list-image-gallery', 'list_image');
            Route::GET('/list-video-gallery', 'list_video');
            Route::POST('/loading-gallery', 'load_items');
            Route::POST('/insert-gallery', 'insert_items');
            Route::POST('/update-image-gallery', 'update_image');
            Route::POST('/update-nameimg-gallery', 'update_name');
            Route::POST('/update-content-gallery', 'update_content');
            Route::POST('/delete-gallery', 'delete_item');
        });
    });

    /* Quản Lý Phòng */
    Route::controller(RoomController::class)->group(function () {
        Route::group(['prefix' => 'admin/hotel/manager/room'], function () {
            Route::GET('/', 'list_items');
            Route::GET('/all-room', 'list_items');
            Route::GET('/search-room', 'search_items');
            Route::GET('/add-room', 'insert_item');
            Route::POST('/save-room', 'save_item');
            Route::GET('/load-room', 'load_items');
            Route::GET('/edit-room', 'edit_item');
            Route::POST('/update-room', 'update_item');
            Route::POST('/update-status-room', 'update_status_item');
            Route::POST('/delete-room', 'move_to_bin');
            Route::GET('/count-bin', 'count_bin');
            Route::GET('/list-deleted-room', 'list_bin');
            Route::GET('/load-deleted-room', 'load_bin');
            Route::GET('/search-bin', 'search_bin');
            Route::POST('/restore-room', 'un_bin');
            Route::POST('/delete-trash-room', 'bin_delete');
        });
    });

    /* Gallery - Thư Viện Ảnh Của Phòng */
    Route::controller(GalleryRoomController::class)->group(function () {
        Route::group(['prefix' => 'admin/hotel/manager/room'], function () {
            Route::GET('/list-image-gallery', 'list_image');
            Route::GET('/loading-gallery', 'load_items');
            Route::POST('/insert-gallery', 'insert_items');
            Route::POST('/update-image-gallery', 'update_image');
            Route::POST('/update-nameimg-gallery', 'update_name');
            Route::POST('/update-content-gallery', 'update_content');
            Route::POST('/delete-gallery', 'delete_item');
        });
    });

    /* Quản Lý Các Lựu Chọn - Loại Phòng */
    Route::controller(TypeRoomController::class)->group(function () {
        Route::group(['prefix' => 'admin/hotel/manager/room/typeroom'], function () {
            Route::GET('/', 'list_items');
            Route::GET('/all-typeroom', 'list_items');
            Route::GET('/search-typeroom', 'search_items');
            Route::GET('/add-typeroom', 'insert_item');
            Route::POST('/save-typeroom', 'save_item');
            Route::GET('/load-typeroom', 'load_items');
            Route::GET('/edit-typeroom', 'edit_item');
            Route::POST('/update-typeroom', 'update_item');
            Route::POST('/update-status-typeroom', 'update_status_item');
            Route::POST('/delete-typeroom', 'move_to_bin');
            Route::GET('/count-bin', 'count_bin');
            Route::GET('/list-deleted-typeroom', 'list_bin');
            Route::GET('/load-deleted-typeroom', 'load_bin');
            Route::GET('/search-bin', 'search_bin');
            Route::POST('/restore-typeroom', 'un_bin');
            Route::POST('/delete-trash-typeroom', 'bin_delete');
        });
    });

    /* Coupon - Mã Giảm Giá*/
    Route::group(['middleware' => 'admin.manager.roles'], function () {
        Route::controller(CouponController::class)->group(function () {
            Route::group(['prefix' => 'admin/coupon'], function () {
                Route::GET('/', 'list_coupon');
                Route::GET('/list-coupon', 'list_coupon');
                Route::GET('/add-coupon', 'add_coupon');
                Route::GET('/edit-coupon', 'edit_coupon');
                Route::POST('/update-coupon', 'update_coupon');
                Route::POST('/save-coupon', 'save_coupon');
                Route::GET('/delete-coupon', 'delete_coupon');
            });
        });
    });

    /*Quản Lý Đánh Giá*/
    Route::controller(EvaluateController::class)->group(function () {
        Route::group(['prefix' => 'admin/evaluate'], function () {
            Route::GET('/', 'list_items');
            Route::GET('/all-evaluate', 'list_items');
            Route::GET('/search-evaluate', 'search_items');
            Route::GET('/load-evaluate', 'load_items');
            Route::POST('/delete-evaluate', 'move_to_bin');
            Route::GET('/count-bin', 'count_bin');
            Route::GET('/list-deleted-evaluate', 'list_bin');
            Route::GET('/load-deleted-evaluate', 'load_bin');
            Route::GET('/search-bin', 'search_bin');
            Route::POST('/restore-evaluate', 'un_bin');
            Route::POST('/delete-trash-evaluate', 'bin_delete');
        });
    });

    /* Quản Lý Đặt Phòng Khách Sạn */
    Route::controller(OrderController::class)->group(function () {
        Route::group(['prefix' => 'admin/order'], function () {
            Route::GET('/', 'list_items');
            Route::GET('/all-order', 'list_items');
            Route::GET('/view-order', 'view_order');
            Route::GET('/search-order', 'search_items');
            Route::GET('/load-order', 'load_items');
            Route::GET('/update-status-order', 'update_status_item');
            Route::POST('/delete-order', 'move_to_bin');
            Route::GET('/count-bin', 'count_bin');
            Route::GET('/list-deleted-order', 'list_bin');
            Route::GET('/load-deleted-order', 'load_bin');
            Route::GET('/search-bin', 'search_bin');
            Route::POST('/restore-order', 'un_bin');
            Route::POST('/delete-trash-order', 'bin_delete');
            Route::GET('/sort-order', 'sort_order');
        });
    });

    /* Nhật Ký Hoạt Động*/
    Route::group(['prefix' => 'admin/activity'], function () {
        /* Nhật Ký Đăng Nhập */
        Route::controller(ActivityLogController::class)->group(function () {
            Route::GET('/all-activity-admin', 'all_activity_admin')->middleware('admin.roles');
            Route::GET('/all-activity-customer', 'all_activity_customer')->middleware('admin.manager.roles');
        });
        /* Nhật Ký Thao Tác */
        Route::controller(ManipulationActivityController::class)->group(function () {
            Route::GET('/all-manipulation-admin', 'all_manipulation_admin')->middleware('admin.roles');
            Route::GET('/all-manipulation-customer', 'all_manipulation_customer')->middleware('admin.manager.roles');
        });
    });

    /* Cấu Hình Website */
    Route::group(['middleware' => 'admin.roles'], function () {
        Route::controller(ConfigWebController::class)->group(function () {
            Route::group(['prefix' => 'admin/web'], function () {
                Route::GET('/', 'show_config');
                Route::POST('/insert-config-image', 'insert_config_image');
                Route::GET('/load-config-slogan', 'loading_config_slogan');
                Route::POST('/edit-config-title', 'edit_config_title');
                Route::POST('/edit-config-content', 'edit_config_content');
                Route::POST('/update-image-config', 'update_image_config');
                Route::GET('/load-logo-config', 'load_logo_config');
                Route::POST('/delete-config', 'delete_config');
                Route::GET('/load-config-brand', 'load_config_brand');
            });
        });
        Route::controller(CompanyConfigController::class)->group(function () {
            Route::group(['prefix' => 'admin/config-footer'], function () {
                Route::GET('/', 'show_company_config');
                Route::GET('/edit-content-footer', 'edit_content_footer');
            });
        });
    });

});

/* Font-End */

/* Đăng Ký - Đăng Nhập - Quên Mật Khẩu ! */
Route::group(['prefix' => 'user'], function () {
    /* Đăng Nhập Tài Khoản Hệ Thống*/
    Route::POST('/login-customer', [LoginAndRegister::class, 'login_customer']);
    /* Đăng Nhập Bằng Tài Khoản Google */
    Route::GET('/login-google', [LoginAndRegister::class, 'login_google']);
    Route::GET('/login-google/callback', [LoginAndRegister::class, 'login_google_callback']);
    /* Đăng Nhập Bằng Tài Khoản Facebook */
    Route::GET('/login-facebook', [LoginAndRegister::class, 'login_facebook']);
    Route::GET('/login-facebook/callback', [LoginAndRegister::class, 'login_facebook_callback']);
    /* Đăng Ký */
    Route::POST('/create-customer', [LoginAndRegister::class, 'create_customer']);
    Route::POST('/verification-code-rg', [LoginAndRegister::class, 'verification_code_rg']);
    Route::GET('/MailToCustomer', [LoginAndRegister::class, 'MailToCustomer']);
    Route::GET('/successful-create-account', [LoginAndRegister::class, 'successful_create_account']);
    /* Quên Mật Khẩu */
    Route::POST('/find-account-recovery-pw', [LoginAndRegister::class, 'find_account_recovery_pw']);
    Route::POST('/verification-code-rc', [LoginAndRegister::class, 'verification_code_rc']);
    Route::POST('/confirm-password', [LoginAndRegister::class, 'confirm_password']);
    /* Đăng Xuất */
    Route::GET('/logout', [LoginAndRegister::class, 'logout']);
});

/* Trang Chủ */
Route::GET('/', [HomeController::class, 'index']);
Route::GET('/inint-value', [HomeController::class, 'inint_value']);

Route::group(['prefix' => '/trang-chu'], function () {
    Route::GET('/', [HomeController::class, 'index']);
    Route::GET('/loading-schedule', [HomeController::class, 'loading_schedule']);
    Route::GET('/update-schedule', [HomeController::class, 'update_schedule']);
});

/* Khách Sạn */
Route::group(['prefix' => '/khach-san'], function () {
    Route::GET('/', [HotelController::class, 'index']);
    Route::GET('/sreach-hotel-place', [HotelController::class, 'sreach_hotel_place']);
});

/*Thông Tin Chi Tiết Khách Sạn */
Route::group(['prefix' => '/khach-san-chi-tiet'], function () {
    Route::GET('/', [HotelController::class, 'DetailsHotel']);
    Route::GET('/chi-tiet-tien-nghi', [HotelController::class, 'detail_convenient_room']);
    Route::GET('/lua-chon-phong', [HotelController::class, 'loading_type_room']);
});

/* Thanh Toán  - Hóa Đơn*/
Route::controller(CheckOutController::class)->group(function () {
    Route::group(['prefix' => 'dat-phong'], function () {
        Route::GET('/', 'show_payment');
        Route::GET('/load-price-list', 'price_list');
        Route::GET('/put-coupon', 'put_coupon');
        Route::GET('/check-coupon', 'check_coupon');
        Route::GET('/special-requirements', 'special_requirements');
        Route::GET('/put-orderer', 'put_orderer');
        Route::GET('/load-coupon', 'load_coupon');
        Route::GET('/unset-coupon', 'unset_coupon');
    });

    Route::group(['prefix' => 'thanh-toan'], function () {
        Route::GET('/momo-payment', 'momo_payment');
        Route::GET('/momo-payment-callback', 'momo_payment_callback');
        Route::GET('/direct-payment', 'direct_payment');
        Route::GET('/hoa-don', 'show_receipt');
        Route::GET('/un-set-order', 'un_set_order');
    });

});

/* Bộ Tìm Kiếm */
Route::controller(SearchMasterController::class)->group(function () {
    Route::group(['prefix' => 'tim-kiem'], function () {
        Route::GET('/', 'show_mastersearch');
        Route::GET('/handle-mastersearch', 'handle_mastersearch');
        Route::GET('/search-name-or-area', 'search_name_or_area');
    });
});

/* Kiểm Tra Đơn Đặt Phòng */
Route::controller(CheckOrderCustomerController::class)->group(function () {
    Route::group(['prefix' => 'kiem-tra-don-hang'], function () {
        Route::GET('/', 'show_check_order');
        Route::GET('/check-order', 'check_order');
        Route::GET('/thong-tin-don-hang', 'order_infomation');
        Route::GET('/loading-order-status', 'loading_order_status');
        Route::GET('/insert-evaluate', 'insert_evaluate');
        Route::GET('/cancel-order', 'cancel_order');
    });
});
