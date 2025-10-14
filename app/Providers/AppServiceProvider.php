<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Slider;
use App\Models\Comment;
use App\Models\Order;
use App\Models\Admin;
use App\Models\ConfigWeb;
use App\Models\CompanyConfig;
use Auth;
use Session;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            \App\Repositories\AdminRepository\AdminRepositoryInterface::class,
            \App\Repositories\AdminRepository\AdminRepository::class,
        );
        $this->app->singleton(
            \App\Repositories\CustomerRepository\CustomerRepositoryInterface::class,
            \App\Repositories\CustomerRepository\CustomerRepository::class,
        );
        $this->app->singleton(
            \App\Repositories\SliderRepository\SliderRepositoryInterface::class,
            \App\Repositories\SliderRepository\SliderRepository::class,
        );
        $this->app->singleton(
            \App\Repositories\BrandRepository\BrandRepositoryInterface::class,
            \App\Repositories\BrandRepository\BrandRepository::class,
        );
        $this->app->singleton(
            \App\Repositories\AreaRepository\AreaRepositoryInterface::class,
            \App\Repositories\AreaRepository\AreaRepository::class,
        );
        $this->app->singleton(
            \App\Repositories\ManagerHotelRepository\ManagerHotelRepositoryInterface::class,
            \App\Repositories\ManagerHotelRepository\ManagerHotelRepository::class,
        );
        $this->app->singleton(
            \App\Repositories\GalleryHotelRepository\GalleryHotelRepositoryInterface::class,
            \App\Repositories\GalleryHotelRepository\GalleryHotelRepository::class,
        );
        $this->app->singleton(
            \App\Repositories\GalleryRoomRepository\GalleryRoomRepositoryInterface::class,
            \App\Repositories\GalleryRoomRepository\GalleryRoomRepository::class,
        );
        $this->app->singleton(
            \App\Repositories\RoomRepository\RoomRepositoryInterface::class,
            \App\Repositories\RoomRepository\RoomRepository::class,
        );
        $this->app->singleton(
            \App\Repositories\TypeRoomRepository\TypeRoomRepositoryInterface::class,
            \App\Repositories\TypeRoomRepository\TypeRoomRepository::class,
        );
        $this->app->singleton(
            \App\Repositories\FacilitiesRoomRepository\FacilitiesRoomRepositoryInterface::class,
            \App\Repositories\FacilitiesRoomRepository\FacilitiesRoomRepository::class,
        );
        $this->app->singleton(
            \App\Repositories\FacilitiesHotelRepository\FacilitiesHotelRepositoryInterface::class,
            \App\Repositories\FacilitiesHotelRepository\FacilitiesHotelRepository::class,
        );
        $this->app->singleton(
            \App\Repositories\BannerADSRepository\BannerADSRepositoryInterface::class,
            \App\Repositories\BannerADSRepository\BannerADSRepository::class,
        );
        $this->app->singleton(
            \App\Repositories\ActivityLogRepository\ActivityLogRepositoryInterface::class,
            \App\Repositories\ActivityLogRepository\ActivityLogRepository::class,
        );
        $this->app->singleton(
            \App\Repositories\ManipulationActivityRepository\ManipulationActivityRepositoryInterface::class,
            \App\Repositories\ManipulationActivityRepository\ManipulationActivityRepository::class,
        );
        $this->app->singleton(
            \App\Repositories\ConfigWebRepository\ConfigWebRepositoryInterface::class,
            \App\Repositories\ConfigWebRepository\ConfigWebRepository::class,
        );
        $this->app->singleton(
            \App\Repositories\CompanyConfigRepository\CompanyConfigRepositoryInterface::class,
            \App\Repositories\CompanyConfigRepository\CompanyConfigRepository::class,
        );
        $this->app->singleton(
            \App\Repositories\ServiceChargeRepository\ServiceChargeRepositoryInterface::class,
            \App\Repositories\ServiceChargeRepository\ServiceChargeRepository::class,
        );
        $this->app->singleton(
            \App\Repositories\OrderRepository\OrderRepositoryInterface::class,
            \App\Repositories\OrderRepository\OrderRepository::class,
        );
        $this->app->singleton(
            \App\Repositories\EvaluateRepository\EvaluateRepositoryInterface::class,
            \App\Repositories\EvaluateRepository\EvaluateRepository::class,
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /* Biến Toàn View */
        view()->composer('*', function ($view) {
            /* Biến Toàn View Của Back-End */

            /* Biến Toàn View Của Font-End */
            $config_logo_web = ConfigWeb::where('config_type', 1)->first();
            $config_slogan_web = ConfigWeb::where('config_type', 2)->orderBy('config_id', "DESC")->take(4)->get();
            $config_brand_web = ConfigWeb::where('config_type', 3)->orderBy('config_id', "DESC")->take(4)->get();
            $company_config = CompanyConfig::first();
            $sliders = Slider::orderby('slider_id','desc')->get();
            $view->with(compact('sliders', 'config_logo_web', 'config_slogan_web', 'config_brand_web', 'company_config'));
        });

        Paginator::useBootstrap();/* Sử dụng Boostrap để làm giao diện cho phân trang */
    }
}
