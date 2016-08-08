<?php

namespace Ry\Categories\Providers;

//use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Mcamara\LaravelLocalization\LaravelLocalizationServiceProvider;
use Baum\Providers\BaumServiceProvider;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Ry\Categories\Models\Categorylang;

class RyServiceProvider extends ServiceProvider
{
	/**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
    	parent::boot($router);
    	
    	$router->bind("category", function($value){
    		return Categorylang::where("path", "=", $value)->first()->category;
    	});
    	
    	/*
    	$this->publishes([    			
    			__DIR__.'/../config/rycategories.php' => config_path('rycategories.php')
    	], "config");  
    	
    	$this->mergeConfigFrom(
	        	__DIR__.'/../config/rycategories.php', 'rycategories'
	    );
    	
    	$this->publishes([
    			__DIR__.'/../assets' => public_path('vendor/rycategories'),
    	], "public");    	
    	*/
    	
    	//ressources
    	$this->loadViewsFrom(__DIR__.'/../ressources/views', 'rycategories');
    	$this->loadTranslationsFrom(__DIR__.'/../ressources/lang', 'rycategories');
    	
    	/*
    	$this->publishes([
    			__DIR__.'/../ressources/views' => resource_path('views/vendor/rycategories'),
    			__DIR__.'/../ressources/lang' => resource_path('lang/vendor/rycategories'),
    	], "ressources");
    	*/
    	
    	$this->publishes([
    			__DIR__.'/../database/factories/' => database_path('factories'),
	        	__DIR__.'/../database/migrations/' => database_path('migrations')
	    ], 'migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(LaravelLocalizationServiceProvider::class);     
        $this->app->register(BaumServiceProvider::class);   
        
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('LaravelLocalization', LaravelLocalization::class);
    }
    
    public function map(Router $router)
    {    	
    	if (! $this->app->routesAreCached()) {
    		$router->group(['namespace' => 'Ry\Categories\Http\Controllers'], function(){
    			require __DIR__.'/../Http/routes.php';
    		});
    	}
    }
}
