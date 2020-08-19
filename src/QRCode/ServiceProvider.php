<?php
/*
 * This file is part of the flyspring/toutiao.
 *
 * (c) abel
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Spring\TouTiao\QRCode;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 *
 * @author abel
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        !isset($app['qrcode']) && $app['qrcode'] = function ($app) {
            return new Client($app);
        };
    }
}
