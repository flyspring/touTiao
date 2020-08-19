<?php
/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Spring\TouTiao;

use Spring\TouTiao\Kernel\ServiceContainer;

/**
 * Class Application.
 *
 * @property \Spring\TouTiao\AccessToken\AccessToken $access_token
 * @property \Spring\TouTiao\Auth\Client $auth
 * @property \Spring\TouTiao\QRCode\Client $qrcode
 * @property \Spring\TouTiao\Message\Client $message
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [
        AccessToken\ServiceProvider::class,
        Auth\ServiceProvider::class,
        QRCode\ServiceProvider::class,
        Message\ServiceProvider::class,
    ];
}
