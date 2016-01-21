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
    	
	    if (\App::environment('local') && file_exists(app_path() . '/localHelper.php')) {
	        include app_path('localHelper.php');
	    }
	    
	    \DB::listen(function($sql, $bindings, $time){
	    
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
