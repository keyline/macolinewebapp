<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PHPMailerService;

class PHPMailerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('PHPMailerService', function ($app) {
            return new PHPMailerService();
        });
    }
}