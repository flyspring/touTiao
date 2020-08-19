<?php

/*
 * 参考 overtrue
 */

return [
    /*
     * 默认配置，将会合并到各模块中
     */
    'defaults' => [
        /*
         * 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
         */
        'response_type' => 'array',

        /*
         * 使用 Laravel 的缓存系统
         */
        'use_laravel_cache' => true,
    ],
    
    'app_id'  => '', //env('TT_MINI_PROGRAM_APPID', ''),
    'app_secret'  => '', //env('TT_MINI_PROGRAM_SECRET', ''),
];
