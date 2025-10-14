<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Roles;
use App\Models\BannerADS;
use Session;
class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::if('hasrole', function($expression){
            if(Auth::check()){
                if(Auth::user()->hasRoles($expression)){
                    return true;
                }
            }
            return false;
        });
        Blade::if('hasanyroles', function($expression){
            if(Auth::check()){
                if(Auth::user()->hasAnyRoles($expression)){
                    return true;
                }
            }
            return false;
        });
        Blade::if('impersonate', function(){
            if(session()->has('impersonate')){
               return true;
            }
            return false;
        });

        Blade::if('BannerADS', function(){
            if(isset($_COOKIE['ADS_BANNER'])){
               return false;
            }
            return true;
        });
        
    }
}
