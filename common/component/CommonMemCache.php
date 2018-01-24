<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\common\component;

use Yii;
use yii\caching\MemCache;

/**
 * MemCache implements a cache application component based on [memcache](http://pecl.php.net/package/memcache)
 * and [memcached](http://pecl.php.net/package/memcached).
 *
 * MemCache supports both [memcache](http://pecl.php.net/package/memcache) and
 * [memcached](http://pecl.php.net/package/memcached). By setting [[useMemcached]] to be true or false,
 * one can let MemCache to use either memcached or memcache, respectively.
 *
 * MemCache can be configured with a list of memcache servers by settings its [[servers]] property.
 * By default, MemCache assumes there is a memcache server running on localhost at port 11211.
 *
 * See [[Cache]] for common cache operations that MemCache supports.
 *
 * Note, there is no security measure to protected data in memcache.
 * All data in memcache can be accessed by any process running in the system.
 *
 * To use MemCache as the cache application component, configure the application as follows,
 *
 * ```php
 * [
 *     'components' => [
 *         'cache' => [
 *             'class' => 'yii\caching\MemCache',
 *             'servers' => [
 *                 [
 *                     'host' => 'server1',
 *                     'port' => 11211,
 *                     'weight' => 60,
 *                 ],
 *                 [
 *                     'host' => 'server2',
 *                     'port' => 11211,
 *                     'weight' => 40,
 *                 ],
 *             ],
 *         ],
 *     ],
 * ]
 * ```
 *
 * In the above, two memcache servers are used: server1 and server2. You can configure more properties of
 * each server, such as `persistent`, `weight`, `timeout`. Please see [[MemCacheServer]] for available options.
 *
 * @property \Memcache|\Memcached $memcache The memcache (or memcached) object used by this cache component.
 * This property is read-only.
 * @property MemCacheServer[] $servers List of memcache server configurations. Note that the type of this
 * property differs in getter and setter. See [[getServers()]] and [[setServers()]] for details.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CommonMemCache extends MemCache
{
   /**
    * 
    * @var 使用memcached的缓存机制类型(true为一致性hash算法，false为余数分布算法，默认为false)
    * 
    */
    public $type = false;

   
    /**
     * Stores a value identified by a key in cache.
     * This is the implementation of the method declared in the parent class.
     *
     * @param string $key the key identifying the value to be cached
     * @param mixed $value the value to be cached.
     * @see \MemcachePool::set()
     * @param integer $duration the number of seconds in which the cached value will expire. 0 means never expire.
     * @return boolean true if the value is successfully stored into cache, false otherwise
     */
    protected function setValue($key, $value, $duration)
    {
        // Use UNIX timestamp since it doesn't have any limitation
        // @see http://php.net/manual/en/memcache.set.php
        // @see http://php.net/manual/en/memcached.expiration.php
        if($duration > 0){
        
            $expire = $this->type?$duration : $duration + time();
        
        }else{
        
            $expire = 0;
        
        }
//         $expire = $duration > 0 ? $duration + time() : 0;

        return $this->useMemcached ? parent::setValue($key, $value, $expire) : parent::setValue($key, $value, 0, $expire);
    }

    /**
     * Stores a value identified by a key into cache if the cache does not contain this key.
     * This is the implementation of the method declared in the parent class.
     *
     * @param string $key the key identifying the value to be cached
     * @param mixed $value the value to be cached
     * @see \MemcachePool::set()
     * @param integer $duration the number of seconds in which the cached value will expire. 0 means never expire.
     * @return boolean true if the value is successfully stored into cache, false otherwise
     */
    protected function addValue($key, $value, $duration)
    {
        // Use UNIX timestamp since it doesn't have any limitation
        // @see http://php.net/manual/en/memcache.set.php
        // @see http://php.net/manual/en/memcached.expiration.php
        
        if($duration > 0){
        
            $expire = $this->type?$duration : $duration + time();
        
        }else{
        
            $expire = 0;
        
        }
        
//         $expire = $duration > 0 ? $duration + time() : 0;

        return $this->useMemcached ? parent::add($key, $value, $expire) : parent::add($key, $value, 0, $expire);
    }
}
