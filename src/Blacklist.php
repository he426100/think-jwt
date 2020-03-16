<?php

namespace xiaodi;

use think\App;
use Lcobucci\JWT\Token;

/**
 * 黑名单
 *
 */
class Blacklist
{
    private $app;

    private $store;

    protected $cacheName = 'blacklist';

    public function __construct(App $app)
    {
        $this->app = $app;

        $this->store = $this->getStore();
    }

    /**
     * 获取 缓存驱动
     *
     * @return void
     */
    public function getStore()
    {
        return $this->app->cache;
    }

    /**
     * 加入黑名单
     *
     * @param Token $token
     * @return void
     */
    public function push(Token $token)
    {
        if (false === $this->has($token)) {
            $claims = $token->getClaims();
            $exp = $claims['exp']->getValue() - time();
            $this->store->push($this->cacheName, (string)$token, $exp);
        }
    }

    /**
     * 是否存在黑名单
     *
     * @param Token $token
     * @return boolean
     */
    public function has(Token $token)
    {
        $blacklist = $this->getAll();
        
        return in_array((string)$token, $blacklist);
    }

    /**
     * 获取所有黑名单
     *
     * @return array
     */
    public function getAll()
    {
        return $this->store->get($this->cacheName, []);
    }
}