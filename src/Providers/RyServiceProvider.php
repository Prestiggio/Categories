<?php

namespace Ry\Categories\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

use Mcamara\LaravelLocalization\LaravelLocalizationServiceProvider;
use Baum\Providers\BaumServiceProvider;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Ry\Categories\Models\Categorylang;
use Ry\Categories\Models\Categorie;
use Ry\Categories\Console\Commands\Categorie as CategorieCommand;
use Ry\Categories\RyCategorie;

class RyServiceProvider extends ServiceProvider
{
	/**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    	/*
    	$this->publishes([    			
    			__DIR__.'/../config/rycategories.php' => config_path('rycategories.php')
    	], "config");
    	
    	$this->mergeConfigFrom(
	        	__DIR__.'/../config/rycategories.php', 'rycategories'
	    );
	    */
    	
    	$this->publishes([
    			__DIR__.'/../assets/templates' => public_path('vendor/rycategories'),
    	], "public");    	
    	
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
    	
    	$this->map();
    	
    	$this->app["router"]->bind("category", function($value){
    		return Categorylang::where("path", "=", $value)->first()->category;
    	});
    	
	    Categorylang::saved(function($term){
	        $term->makepath();
	    });
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
        $this->app->singleton("rycategories.addgroup", function($app){
        	return new CategorieCommand();
        });
        $this->commands("rycategories.addgroup");
        $this->app->singleton("rycategories", function($app){
        	return new RyCategorie();
        });
    }
    
    public function map()
    {    	
    	if (! $this->app->routesAreCached()) {
    		$this->app["router"]->group(['namespace' => 'Ry\Categories\Http\Controllers'], function(){
    			require __DIR__.'/../Http/routes.php';
    		});
    	}
    }
}
