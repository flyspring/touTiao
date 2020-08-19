<?php

/*
 * This file is part of the flyspring/toutiao for laravel.
 *
 * (c) abel
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Spring\TouTiao;

use Spring\TouTiao\Application as MiniProgram;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;


/**
 * Class ServiceProvider.
 *
 * @author abel
 */
class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Boot the provider.
     */
    public function boot()
    {
    }
    
    /**
     * Register the provider.
     */
    public function register()
    {
        $this->app->singleton('toutiao.mini_program', function($app) {
            $config = $app['config']['toutiao.mini_program.default'];
            $miniProgram = new MiniProgram($config);
            if ($app['config']['toutiao.defaults.use_laravel_cache']) {
                $miniProgram['cache'] = new CacheBridge($app['cache.store']);
                $miniProgram['request'] = $app['request'];
            }
            return $miniProgram;
        });
        
        $this->app->alias('toutiao.mini_program', MiniProgram::class);
    }
}
