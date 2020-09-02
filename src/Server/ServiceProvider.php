<?php
/*
 * This file is part of the Spring/TouTiao.
 *
 * (c) abel
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Spring\TouTiao\Server;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Spring\TouTiao\Encryptor;

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
        !isset($app['encryptor']) && $app['encryptor'] = function ($app) {
            return new Encryptor($app['config']['aes_key']);
        };
    }
}
