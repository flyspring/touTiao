<?php
/*
 * This file is part of the Spring/TouTiao.
 *
 * (c) abel
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Spring\TouTiao\Kernel;

use Spring\TouTiao\Kernel\Contracts\AccessTokenInterface;
use Spring\TouTiao\Kernel\Exceptions\HttpException;
use Spring\TouTiao\Kernel\Exceptions\InvalidArgumentException;
use Spring\TouTiao\Kernel\Exceptions\RuntimeException;
use Spring\TouTiao\Kernel\Traits\HasHttpRequests;
use Spring\TouTiao\Kernel\Traits\InteractsWithCache;
use Pimple\Container;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class AccessToken.
 *
 * @author abel
 */
abstract class AccessToken implements AccessTokenInterface
{
    use HasHttpRequests;
    use InteractsWithCache;
    /**
     * @var \Pimple\Container
     */
    protected $app;
    /**
     * @var string
     */
    protected $requestMethod = 'GET';
    /**
     * @var string
     */
    protected $endpointToGetToken;
    /**
     * @var string
     */
    protected $queryName;
    /**
     * @var array
     */
    protected $token;

    /**
     * @var int
     */
    protected $safeSeconds = 500;
    /**
     * @var string
     */
    protected $tokenKey = 'access_token';
    /**
     * @var string
     */
    protected $cachePrefix = 'spring.tt.kernel.access_token.';

    /**
     * AccessToken constructor.
     *
     * @param \Pimple\Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * @param bool $refresh
     *
     * @throws \Spring\TouTiao\Kernel\Exceptions\HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Spring\TouTiao\Kernel\Exceptions\InvalidConfigException
     * @throws \Spring\TouTiao\Kernel\Exceptions\InvalidArgumentException
     * @throws \Spring\TouTiao\Kernel\Exceptions\RuntimeException
     *
     * @return array
     */
    public function getToken(bool $refresh = false): array
    {
        $cache = $this->getCache();
        if ($cache) {
            $cacheKey = $this->getCacheKey();
            if (!$refresh && $cache->has($cacheKey)) {
                return $cache->get($cacheKey);
            }
        }
        
        /** @var array $token */
        $token = $this->requestToken($this->getCredentials(), true);
        
        if ($cache) {
            $this->setToken($token[$this->tokenKey], $token['expires_in'] ?? 7200);
        }
        
        return $token;
    }

    /**
     * @param string $token
     * @param int    $lifetime
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Spring\TouTiao\Kernel\Exceptions\RuntimeException
     *
     * @return \Spring\TouTiao\Kernel\Contracts\AccessTokenInterface
     */
    public function setToken(array $result, $lifetime = 7200): AccessTokenInterface
    {
        $cache = $this->getCache();
        if ($cache) {
            $cache->set($this->getCacheKey(), [
                $this->tokenKey => $token,
                'expires_in' => $lifetime,
            ], $lifetime - $this->safeSeconds);
        }
        
        return $this;
    }

    /**
     * @throws \Spring\TouTiao\Kernel\Exceptions\HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Spring\TouTiao\Kernel\Exceptions\InvalidConfigException
     * @throws \Spring\TouTiao\Kernel\Exceptions\InvalidArgumentException
     * @throws \Spring\TouTiao\Kernel\Exceptions\RuntimeException
     *
     * @return \Spring\TouTiao\Kernel\Contracts\AccessTokenInterface
     */
    public function refresh(): AccessTokenInterface
    {
        $this->getToken(true);

        return $this;
    }

    /**
     * @param array $credentials
     * @param bool  $toArray
     *
     * @throws \Spring\TouTiao\Kernel\Exceptions\HttpException
     * @throws \Spring\TouTiao\Kernel\Exceptions\InvalidConfigException
     * @throws \Spring\TouTiao\Kernel\Exceptions\InvalidArgumentException
     *
     * @return \Psr\Http\Message\ResponseInterface|\Spring\TouTiao\Kernel\Support\Collection|array|object|string
     */
    public function requestToken(array $credentials, $toArray = false)
    {
        $response = $this->sendRequest($credentials);
        $result = json_decode($response->getBody()->getContents(), true);
        $formatted = $this->castResponseToType($response, $this->app['config']->get('response_type'));
        if (empty($result[$this->tokenKey])) {
            throw new HttpException('Request access_token fail: '.json_encode($result, JSON_UNESCAPED_UNICODE), $response, $formatted);
        }

        return $toArray ? $result : $formatted;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param array                              $requestOptions
     *
     * @throws \Spring\TouTiao\Kernel\Exceptions\HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Spring\TouTiao\Kernel\Exceptions\InvalidConfigException
     * @throws \Spring\TouTiao\Kernel\Exceptions\InvalidArgumentException
     * @throws \Spring\TouTiao\Kernel\Exceptions\RuntimeException
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function applyToRequest(RequestInterface $request, array $requestOptions = []): RequestInterface
    {
        parse_str($request->getUri()->getQuery(), $query);
        $query = http_build_query(array_merge($this->getQuery(), $query));

        return $request->withUri($request->getUri()->withQuery($query));
    }

    /**
     * Send http request.
     *
     * @param array $credentials
     *
     * @throws \Spring\TouTiao\Kernel\Exceptions\InvalidArgumentException
     *
     * @return ResponseInterface
     */
    protected function sendRequest(array $credentials): ResponseInterface
    {
        $options = [
            ('GET' === $this->requestMethod) ? 'query' : 'json' => $credentials,
        ];

        return $this->setHttpClient($this->app['http_client'])->request($this->getEndpoint(), $this->requestMethod, $options);
    }

    /**
     * @return string
     */
    protected function getCacheKey()
    {
        return $this->cachePrefix.md5(json_encode($this->getCredentials()));
    }

    /**
     * The request query will be used to add to the request.
     *
     * @throws \Spring\TouTiao\Kernel\Exceptions\HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Spring\TouTiao\Kernel\Exceptions\InvalidConfigException
     * @throws \Spring\TouTiao\Kernel\Exceptions\InvalidArgumentException
     * @throws \Spring\TouTiao\Kernel\Exceptions\RuntimeException
     *
     * @return array
     */
    protected function getQuery(): array
    {
        return [$this->queryName ?? $this->tokenKey => $this->getToken()[$this->tokenKey]];
    }

    /**
     * @throws \Spring\TouTiao\Kernel\Exceptions\InvalidArgumentException
     *
     * @return string
     */
    public function getEndpoint(): string
    {
        if (empty($this->endpointToGetToken)) {
            throw new InvalidArgumentException('No endpoint for access token request.');
        }

        return $this->endpointToGetToken;
    }

    /**
     * @return string
     */
    public function getTokenKey()
    {
        return $this->tokenKey;
    }
    
    /**
     * @return string
     */
    public function getTokenValue(): string
    {
        return $this->getToken()[$this->tokenKey];
    }

    /**
     * Credential for get token.
     *
     * @return array
     */
    abstract protected function getCredentials(): array;
}
