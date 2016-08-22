<?php

namespace Elixir\Config;

use Elixir\Config\Cache\CacheableInterface;
use Elixir\Config\Loader\LoaderFactory;
use Elixir\Config\Loader\LoaderFactoryAwareTrait;
use Elixir\Config\Processor\ProcessorInterface;
use Elixir\Config\Writer\WriterInterface;
use function Elixir\STDLib\array_get;
use function Elixir\STDLib\array_has;
use function Elixir\STDLib\array_remove;
use function Elixir\STDLib\array_set;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Config implements ConfigInterface, CacheableInterface, \Iterator, \Countable
{
    use LoaderFactoryAwareTrait;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var CacheableInterface
     */
    protected $cache;

    /**
     * @var ProcessorInterface
     */
    protected $processor;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param string $environment
     * @param array  $data
     */
    public function __construct($environment = null, array $data = [])
    {
        $this->environment = $environment;
        $this->data = $data;
    }

    /**
     * @param CacheableInterface $value
     */
    public function setCacheStrategy(CacheableInterface $value)
    {
        $this->cache = $value;
    }

    /**
     * @return CacheableInterface
     */
    public function getCacheStrategy()
    {
        return $this->cache;
    }

    /**
     * @param ProcessorInterface $value
     */
    public function setProcessor(ProcessorInterface $value)
    {
        $this->processor = $value;
    }

    /**
     * @return ProcessorInterface
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * {@inheritdoc}
     */
    public function loadCache()
    {
        if (null === $this->cache) {
            return false;
        }

        $data = $this->cache->loadCache();

        if ($data) {
            $this->merge($data);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function cacheLoaded()
    {
        if (null === $this->cache) {
            return false;
        }

        return $this->cache->cacheLoaded();
    }

    /**
     * {@inheritdoc}
     */
    public function load($config, array $options = [])
    {
        if ($this->cacheLoaded() && $this->isFreshCache()) {
            return;
        }

        if ($config instanceof self) {
            $this->merge($config);
        } else {
            if (is_callable($config)) {
                $data = call_user_func_array($config, [$this]);
            } else {
                $options['environment'] = $this->environment;

                if (null === $this->loaderFactory) {
                    $this->loaderFactory = new LoaderFactory();
                    LoaderFactory::addProvider($this->loaderFactory);
                }

                $loader = $this->loaderFactory->create($config, $options);
                $data = $loader->load($config);
            }

            $this->merge($data);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function export(WriterInterface $writer, $file)
    {
        return $writer->export($this->all(), $file);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return array_has($key, $this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $data = array_get($key, $this->data, $default);

        if (null !== $this->processor) {
            $data = $this->processor->process($data);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        array_set($key, $value, $this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        array_remove($key, $this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $data = $this->data;

        if (null !== $this->processor) {
            $data = $this->processor->process($data);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $data)
    {
        $this->data = $data;
    }

    /**
     * @ignore
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * @ignore
     */
    public function offsetSet($key, $value)
    {
        if (null === $key) {
            throw new \InvalidArgumentException('The key can not be undefined.');
        }

        $this->set($key, $value);
    }

    /**
     * @ignore
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * @ignore
     */
    public function offsetUnset($key)
    {
        $this->remove($key);
    }

    /**
     * @ignore
     */
    public function rewind()
    {
        return reset($this->data);
    }

    /**
     * @ignore
     */
    public function current()
    {
        return $this->get($this->key());
    }

    /**
     * @ignore
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * @ignore
     */
    public function next()
    {
        return next($this->data);
    }

    /**
     * @ignore
     */
    public function valid()
    {
        return null !== $this->key();
    }

    /**
     * @ignore
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function isFreshCache()
    {
        if (null === $this->cache) {
            return false;
        }

        return $this->cache->isFreshCache();
    }

    /**
     * {@inheritdoc}
     */
    public function exportToCache(array $data = null)
    {
        if (null === $this->cache) {
            return false;
        }

        if ($data) {
            $this->merge($data);
        }

        return $this->cache->exportToCache($this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function invalidateCache()
    {
        if (null === $this->cache) {
            return false;
        }

        return $this->cache->invalidateCache();
    }

    /**
     * {@inheritdoc}
     */
    public function merge($data)
    {
        if ($data instanceof self) {
            $data = $data->all();
        }

        $this->data = array_merge($this->data, $data);
    }

    /**
     * @ignore
     */
    public function __debugInfo()
    {
        return [
            'environment' => $this->environment,
            'data' => $this->data,
        ];
    }
}
