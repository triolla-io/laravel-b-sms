<?php

namespace Yna\BSms;

use Illuminate\Support\ServiceProvider;

class BSmsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(BSmsApi::class, function () {
            $config = config('services.bsms');

            return new BSmsApi(
                $config['user'],
                $config['password'],
                $config['sender']
            );
        });
    }
}
