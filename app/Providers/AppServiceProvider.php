<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        \Validator::extend('mobile', function ($attribute, $value, $parameters) {
            return mobileCheck($value);
        });
        
    	\Validator::extend('identity', function ($attribute, $value, $parameters) {
    	    return identityCardCheck($value);
    	});
    	
    	\App::environment('local') && \DB::enableQueryLog();
            
        \DB::enableQueryLog();
        
        \DB::listen(function ($event) {
            \App::environment('local') && \App\Services\ServiceLog::sqlLog($event->sql, $event->bindings, $event->time);
        });
        
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
