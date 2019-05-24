<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AuthService extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //app_path es la ruta de la app + Ruta del Helper
        require_once app_path().'/Helpers/JwtAuth.php'; 
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
