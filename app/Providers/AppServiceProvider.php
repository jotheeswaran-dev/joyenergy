<?php

namespace App\Providers;

use App\Helpers\ModelHelper;
use App\Models\MeterReadings;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $data = array("id" => 1, "entries" => [["name" => "san", "value" => 100], ["name" => "sandy", "value" => 200]]);
        config(['joyconfig.data' => $data]);
    }
}
