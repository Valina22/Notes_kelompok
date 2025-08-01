<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace CodeIgniter\Cache\Handlers;

use CodeIgniter\Exceptions\BadMethodCallException;
use CodeIgniter\Exceptions\CriticalError;
use CodeIgniter\I18n\Time;
use Config\Cache;
use Exception;
use Memcache;
use Memcached;

/**
 * Mamcached cache handler
 *
 * @see \CodeIgniter\Cache\Handlers\MemcachedHandlerTest
 */
class MemcachedHandler extends BaseHandler
{
    /**
     * The memcached object
     *
     * @var Memcache|Memcached
     */
    protected $memcached;

    /**
     * Memcached Configuration
     *
     * @var array{host: string, port: int, weight: int, raw: bool}
     */
    protected $config = [
        'host'   => '127.0.0.1',
        'port'   => 11211,
        'weight' => 1,
        'raw'    => false,
    ];

    /**
     * Note: Use `CacheFactory::getHandler()` to instantiate.
     */
    public function __construct(Cache $config)
    {
        $this->prefix = $config->prefix;

        $this->config = array_merge($this->config, $config->memcached);
    }

    /**
     * Closes the connection to Memcache(d) if present.
     */
    public function __destruct()
    {
        if ($this->memcached instanceof Memcached) {
            $this->memcached->quit();
        } elseif ($this->memcached instanceof Memcache) {
            $this->memcached->close();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        try {
            if (class_exists(Memcached::class)) {
                $this->memcached = new Memcached();

                if ($this->config['raw']) {
                    $this->memcached->setOption(Memcached::OPT_BINARY_PROTOCOL, true);
                }

                $this->memcached->addServer(
                    $this->config['host'],
                    $this->config['port'],
                    $this->config['weight'],
                );

                $stats = $this->memcached->getStats();

                // $stats should be an associate array with a key in the format of host:port.
                // If it doesn't have the key, we know the server is not working as expected.
                if (! is_array($stats) || ! isset($stats[$this->config['host'] . ':' . $this->config['port']])) {
                    throw new CriticalError('Cache: Memcached connection failed.');
                }
            } elseif (class_exists(Memcache::class)) {
                $this->memcached = new Memcache();

                if (! $this->memcached->connect($this->config['host'], $this->config['port'])) {
                    throw new CriticalError('Cache: Memcache connection failed.');
                }

                $this->memcached->addServer(
                    $this->config['host'],
                    $this->config['port'],
                    true,
                    $this->config['weight'],
                );
            } else {
                throw new CriticalError('Cache: Not support Memcache(d) extension.');
            }
        } catch (Exception $e) {
            throw new CriticalError('Cache: Memcache(d) connection refused (' . $e->getMessage() . ').');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $key)
    {
        $data = [];
        $key  = static::validateKey($key, $this->prefix);

        if ($this->memcached instanceof Memcached) {
            $data = $this->memcached->get($key);

            // check for unmatched key
            if ($this->memcached->getResultCode() === Memcached::RES_NOTFOUND) {
                return null;
            }
        } elseif ($this->memcached instanceof Memcache) {
            $flags = false;
            $data  = $this->memcached->get($key, $flags);

            // check for unmatched key (i.e. $flags is untouched)
            if ($flags === false) {
                return null;
            }
        }

        return is_array($data) ? $data[0] : $data;
    }

    /**
     * {@inheritDoc}
     */
    public function save(string $key, $value, int $ttl = 60)
    {
        $key = static::validateKey($key, $this->prefix);

        if (! $this->config['raw']) {
            $value = [
                $value,
                Time::now()->getTimestamp(),
                $ttl,
            ];
        }

        if ($this->memcached instanceof Memcached) {
            return $this->memcached->set($key, $value, $ttl);
        }

        if ($this->memcached instanceof Memcache) {
            return $this->memcached->set($key, $value, 0, $ttl);
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $key)
    {
        $key = static::validateKey($key, $this->prefix);

        return $this->memcached->delete($key);
    }

    /**
     * {@inheritDoc}
     *
     * @return never
     */
    public function deleteMatching(string $pattern)
    {
        throw new BadMethodCallException('The deleteMatching method is not implemented for Memcached. You must select File, Redis or Predis handlers to use it.');
    }

    /**
     * {@inheritDoc}
     */
    public function increment(string $key, int $offset = 1)
    {
        if (! $this->config['raw']) {
            return false;
        }

        $key = static::validateKey($key, $this->prefix);

        return $this->memcached->increment($key, $offset, $offset, 60);
    }

    /**
     * {@inheritDoc}
     */
    public function decrement(string $key, int $offset = 1)
    {
        if (! $this->config['raw']) {
            return false;
        }

        $key = static::validateKey($key, $this->prefix);

        // FIXME: third parameter isn't other handler actions.

        return $this->memcached->decrement($key, $offset, $offset, 60);
    }

    /**
     * {@inheritDoc}
     */
    public function clean()
    {
        return $this->memcached->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getCacheInfo()
    {
        return $this->memcached->getStats();
    }

    /**
     * {@inheritDoc}
     */
    public function getMetaData(string $key)
    {
        $key    = static::validateKey($key, $this->prefix);
        $stored = $this->memcached->get($key);

        // if not an array, don't try to count for PHP7.2
        if (! is_array($stored) || count($stored) !== 3) {
            return false; // @TODO This will return null in a future release
        }

        [$data, $time, $limit] = $stored;

        return [
            'expire' => $limit > 0 ? $time + $limit : null,
            'mtime'  => $time,
            'data'   => $data,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function isSupported(): bool
    {
        return extension_loaded('memcached') || extension_loaded('memcache');
    }
}
