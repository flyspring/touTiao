<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Spring\TouTiao\Auth;

use Spring\TouTiao\Kernel\BaseClient;

/**
 * Class Auth.
 *
 * @author abel
 */
class Client extends BaseClient
{
    /**
     * Get session info by code.
     *
     * @param string $code
     *
     * @throws \Spring\TouTiao\Kernel\Exceptions\InvalidConfigException
     *
     * @return \Psr\Http\Message\ResponseInterface|\Spring\TouTiao\Kernel\Support\Collection|array|object|string
     */
    public function session(string $code, bool $anonymous = false)
    {
        $params = [
            'appid'  => $this->app['config']['app_id'],
            'secret' => $this->app['config']['app_secret'],
        ];
        if ($anonymous) {
            $params['anonymous_code'] = $code;
        } else {
            $params['code'] = $code;
        }

        return $this->httpGet('api/apps/jscode2session', $params);
    }
}
