<?php
/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Spring\TouTiao\AccessToken;

use Spring\TouTiao\Kernel\AccessToken as BaseAccessToken;

/**
 * Class AuthorizerAccessToken.
 *
 * @author overtrue <i@overtrue.me>
 */
class AccessToken extends BaseAccessToken
{
    /**
     * @var string
     */
    protected $endpointToGetToken = 'https://developer.toutiao.com/api/apps/token';

    /**
     * @return array
     */
    protected function getCredentials(): array
    {
        $clientSecrect = $this->app['config']['app_secret'];
        date_default_timezone_set('Asia/Shanghai');
        $clientId = $this->app['config']['app_id'];
        $grandType = 'client_credential';

        return [
            'grant_type' => $grandType,
            'appid'      => $clientId,
            'secret'     => $clientSecrect,
        ];
    }
}
