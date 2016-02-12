<?php

namespace Elixir\Config;

use Elixir\Config\Cache\CacheableInterface;
use Elixir\Config\ConfigInterface;
use Elixir\Config\Loader\LoaderFactory;
use Elixir\Config\Processor\ProcessorInterface;
use Elixir\Config\Writer\WriterInterface;
use Elixir\STDLib\ArrayUtils;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Config implements ConfigInterface, CacheableInterface, \Iterator, \Countable
{
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
     * @param array $data
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
        $this->cache->setConfig($this);
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
     * @throws \LogicException
     */
    public function setConfig(ConfigInterface $value) 
    {
        throw new \LogicException('Needless to self inject.');
    }
    
    /**
     * {@inheritdoc}
     */
    public function loadCache()
    {
        if (null === $this->cache)
        {
            return false;
        }
        
        return $this->cache->loadCache();
    }
    
    /**
     * {@inheritdoc}
     */
    public function cacheLoaded()
    {
        if (null === $this->cache)
        {
            return false;
        }
        
        return null !== $this->cache->cacheLoaded();
    }
    
    /**
     * {@inheritdoc}
     */
    public function load($config, array $options = [])
    {
        $options['recursive'] = isset($options['recursive']) ? $options['recursive'] : false;
        
        if ($config instanceof self)
        {
            $data = $config->all();
        } 
        else 
        {
            $data = [];
            $options['environment'] = $this->environment;
            
            foreach ((array)$config as $conf) 
            {
                if (is_callable($conf))
                {
                    $conf = call_user_func($conf);
                }
                
                if (is_array($conf))
                {
                    $this->load($conf);
                    continue;
                }
                
                $d = null;
                
                if (null !== $this->cache && is_file($conf))
                {
                    $d = $this->cache->loadFromCache($conf, $options);
                    
                    if (true === $d)
                    {
                        continue;
                    }
                }
                
                if (!$d)
                {
                    $loader = LoaderFactory::create($conf, $options);
                    $d = $loader->load($conf, $options['recursive']);
                }
                
                $data = $options['recursive'] ? array_merge_recursive($data, $d) : array_merge($data, $d);
            }
            
            $this->merge($data, $options['recursive']);
            return $data;
        }
    }
    
    /**
     * {@inheritdoc}
     * @throws \RuntimeException
     */
    public function loadFromCache($file, array $options = [])
    {
        if (null === $this->cache)
        {
            throw new \RuntimeException('Cache strategy is not defined');
        }
        
        $options['environment'] = $this->environment;
        $options['recursive'] = isset($options['recursive']) ? $options['recursive'] : false;
        
        $data = $this->cache->loadFromCache($file, $options);
        $this->merge($data, $options['recursive']);
        
        return $data;
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
        return ArrayUtils::has($key, $this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null) 
    {
        $data = ArrayUtils::get($key, $this->data, $default);
        
        if (null !== $this->processor)
        {
            $data = $this->processor->process($data);
        }
        
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value) 
    {
        ArrayUtils::set($key, $value, $this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key) 
    {
        ArrayUtils::remove($key, $this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function all() 
    {
        $data = $this->data;
        
        if (null !== $this->processor)
        {
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
        if (null === $key)
        {
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
    public function exportToCache()
    {
        if (null === $this->cache)
        {
            return false;
        }
        
        return $this->cache->exportToCache();
    }
    
    /**
     * {@inheritdoc}
     */
    public function invalidateCache()
    {
        if (null === $this->cache)
        {
            return false;
        }
        
        return $this->cache->exportToCache();
    }

    /**
     * {@inheritdoc}
     */
    public function merge($data, $recursive = false) 
    {
        if ($data instanceof self) 
        {
            $data = $data->all();
        }

        $this->data = $recursive ? array_merge_recursive($this->data, $data) : array_merge($this->data, $data);
    }
    
    /**
     * @ignore
     */
    public function __debugInfo()
    {
        return [
            'environment' => $this->environment,
            'data' => $this->data
        ];
    }
}
