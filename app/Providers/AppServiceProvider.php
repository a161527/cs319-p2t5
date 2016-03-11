<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Validators\RestValidator;

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

        \Validator::resolver(function($translator, $data, $rules, $messages)
        {
            return new RestValidator($translator, $data, $rules, $messages);
        });

        \Validator::extend('idarray', function($attr, $value, $params) {
            if (!is_array($value)) {
                return false;
            }

            foreach ($value as $v) {
                if (!is_int($v) || (int) $v < 1) return false;
            }
            return true;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
