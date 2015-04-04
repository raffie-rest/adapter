<?php namespace Raffie\REST\Adapter;
 
use Illuminate\Support\ServiceProvider;
 
class AdapterServiceProvider extends ServiceProvider {
 
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->publishes([
            __DIR__.'/config/rest_resources.php' => config_path('rest_resources.php'),
        ]);
    }
 
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}