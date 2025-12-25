<?php

use App\Http\Controllers\AIHotelController;
use App\Http\Controllers\ApiAreaController;
use App\Http\Controllers\ApiBannerController;
use App\Http\Controllers\ApiCheckoutController;
use App\Http\Controllers\ApiCouponController;
use App\Http\Controllers\ApiCustomerController;
use App\Http\Controllers\ApiHotelController;
use App\Http\Controllers\ApiOrderHotelController;
use App\Http\Controllers\ApiOrderRestaurantController;
use App\Http\Controllers\ApiRestaurantController;
use App\Http\Controllers\ApiSearchController;
use App\Http\Controllers\ApiSlideController;
use App\Http\Controllers\ApiSloganController;
use App\Http\Controllers\ApiVnpayController;
use App\Http\Controllers\VnpayController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/slides', [ApiSlideController::class, 'getSlides']);
Route::get('/slogans', [ApiSloganController::class, 'getSlogans']);
Route::get('/areas', [ApiAreaController::class, 'getAreas']);
Route::get('/hotels', [ApiHotelController::class, 'getHotels']);
Route::get('/hotel/flashsales', [ApiHotelController::class, 'getFlashSaleHotels']);
Route::get('/coupons', [ApiCouponController::class, 'getCoupons']);
Route::get('/hotels/{hotel_id}/evaluations', [ApiHotelController::class, 'getEvaluateHotelByID']);
Route::get('/hotels/{hotel_id}/details', [ApiHotelController::class, 'getDetailsHotelByID']);
Route::get('/hotels/{hotel_id}/rooms', [ApiHotelController::class, 'getRoomHotelByID']);
Route::get('/hotels/payment/{typeroom_id}', [ApiCheckoutController::class, 'getTypeRoomByID']);

Route::prefix('hotel')->group(function () {
    Route::get('/search', [ApiSearchController::class, 'search']);
    Route::get('master-search', [ApiSearchController::class, 'masterSearch']);
    Route::get('/filter-search', [ApiHotelController::class, 'filterSearch']);
    Route::post('/semantic-search', [ApiHotelController::class, 'semanticSearch']);
});

Route::prefix('order')->group(function () {
    Route::get('/list', [ApiOrderHotelController::class, 'listOrders'])->middleware('auth:api');
    Route::get('/details', [ApiOrderHotelController::class, 'getOrderDetail'])->middleware('auth:api');
    Route::post('/cancel', [ApiOrderHotelController::class, 'cancelOrder'])->middleware('auth:api');
    Route::put('/status', [ApiOrderHotelController::class, 'updateOrderStatus'])->middleware('auth:api');
    Route::get('/statistics', [ApiOrderHotelController::class, 'getOrderStatistics'])->middleware('auth:api');
    
    // checkOut
    Route::post('/checkout', [ApiCheckoutController::class, 'orderRoom'])->middleware('auth:api');  
});

Route::prefix('/ai')->group(function () {
    Route::get('/hotel-recommendation-popular', [AIHotelController::class, 'getHotelRecommendationPopular']);
    Route::get('/hotel-recommendation-similar/{hotel_id}', [AIHotelController::class, 'getHotelRecommendationForSimilar']);
}); 

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [App\Http\Controllers\ApiAuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\ApiAuthController::class, 'logout'])->middleware('auth:api');
Route::get('/get-profile', [App\Http\Controllers\ApiAuthController::class, 'getProfile'])->middleware('auth:api');


Route::get('admin/category/all-category', 'App\Http\Controllers\APICategoryProduct@all_category');
Route::get('/get-brand', 'App\Http\Controllers\APITestController@getAPi');

// User
Route::post('/check-login', [ApiCustomerController::class, 'logIn']);
Route::post('/create-customer', [ApiCustomerController::class, 'createCustomer']);
Route::post('/login-gg', [ApiCustomerController::class, 'logInGG']);

Route::post('/customer/update-customer', [ApiCustomerController::class, 'updateCustomer']);

//Area
Route::get('/area/get-area-list-have-hotel', [ApiAreaController::class, 'getAreaListHaveHotel']);

//Hotel
Route::get('/hotel/get-hotel-list-by-type', [ApiHotelController::class, 'getHotelList']);
Route::get('/hotel/get-hotel-list-by-area', [ApiHotelController::class, 'getHotelListByArea']);
Route::get('/hotel/get-hotel-by-id', [ApiHotelController::class, 'getHotelById']);
Route::post('/hotel/get-hotel-favourite-list', [ApiHotelController::class, 'getHotelFavouriteList']);
Route::post('/hotel/search-hotel', [ApiSearchController::class, 'handle_mastersearch']);
Route::get('/hotel/hotel-recomendation', [ApiHotelController::class, 'Recommendation']);

//Restaurant
Route::get('/restaurant/restaurant-by-area', [ApiRestaurantController::class, 'getRestaurantByArea']);
Route::get('/restaurant/restaurant-by-id', [ApiRestaurantController::class, 'getRestaurantById']);
Route::post('/restaurant/get-favourite-list', [ApiRestaurantController::class, 'getRestaurantFavourite']);

// Search
Route::post('/search/search-all', [ApiSearchController::class, 'search']);
Route::post('/search/filter-search', [ApiSearchController::class, 'filterSearch']);

//Banner
Route::get('/banner/get-banner-list', [ApiBannerController::class, 'getBannerList']);

//Order
Route::get('/order/get-order-list-by-status', [ApiOrderHotelController::class, 'getOrderListByCustomerId']);


Route::post('/order/cancel-order-by-customer', [ApiOrderHotelController::class, 'cancelOrderByCustomer']);

Route::post('/order/cancel-order-restaurant-by-customer', [ApiOrderRestaurantController::class, 'cancelOrderByCustomer']);

Route::post('/order/evaluate-customer', [ApiOrderHotelController::class, 'evaluateCustomer']);

Route::get('/order/get-order-restaurant-list-by-status', [ApiOrderRestaurantController::class, 'getOrderListByCustomerId']);

// Coupon
Route::get('/coupon/get-coupon', [ApiCouponController::class, 'getCoupons']);


Route::post('/order/checkout-restaurant', [ApiCheckoutController::class, 'orderRestaurant']);

// Brand
Route::get('/brand/get-brand', [ApiSearchController::class, 'getBrand']);
Route::get('/brands', [ApiSearchController::class, 'getBrand']);
Route::get('/hotel-types', [ApiSearchController::class, 'getHotelTypes']);

// Payment - VNPAY
Route::post('/payment/vnpay/create', [ApiVnpayController::class, 'createPayment']);
Route::get('/payment/vnpay/return', [ApiVnpayController::class, 'handleReturn'])->name('api.payment.vnpay.return');
Route::match(['get', 'post'], '/payment/vnpay/ipn', [ApiVnpayController::class, 'handleIpn'])->name('api.payment.vnpay.ipn');
Route::get('/payment/vnpay-callback', [ApiVnpayController::class, 'vnpayPaymentCallback'])->name('api.payment.vnpay.callback');
Route::post('/vnpay-ipn', [VnpayController::class, 'ipn']);
