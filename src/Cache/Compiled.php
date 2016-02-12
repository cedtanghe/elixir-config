<?php

namespace Elixir\Config\Cache;

use Elixir\Config\Cache\CacheableInterface;
use Elixir\Config\ConfigInterface;
use Elixir\Config\Loader\LoaderFactory;
use Elixir\Config\Writer\WriterFactory;
use Elixir\Config\Writer\WriterInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Compiled implements CacheableInterface 
{
    /**
     * @var string|numeric|null
     */
    protected $cacheVersion = null;
    
    /**
     * @var string 
     */
    protected $path;

    /**
     * @var string 
     */
    protected $file;

    /**
     * @var boolean 
     */
    protected $build = false;
    
    /**
     * @var boolean 
     */
    protected $loaded = false;

    /**
     * @var WriterInterface 
     */
    protected $writer;
    
    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var array 
     */
    protected $cachedata;

    /**
     * @param string $path
     * @param string $file
     * @param string|numeric|null $cacheVersion
     */
    public function __construct($path = null, $file = 'cache.php', $cacheVersion = null) 
    {
        $path = $path ? : 'application' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;
        
        if (!is_dir($path)) 
        {
            mkdir($path, 0777, true);
        }

        $this->path = rtrim($path, DIRECTORY_SEPARATOR);
        $this->file = $file;
        $this->cacheVersion = $cacheVersion;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setCacheVersion($value)
    {
        $this->cacheVersion = $value;
    }
    
    /**
     * @return string|numeric|null
     */
    public function getCacheVersion()
    {
        return $this->cacheVersion;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setConfig(ConfigInterface $value)
    {
        $this->config = $value;
    }
    
    /**
     * @param WriterInterface $value
     */
    public function setWriter(WriterInterface $value)
    {
        $this->writer = $value;
    }
    
    /**
     * @return WriterInterface
     */
    public function getWriter()
    {
        return $this->writer;
    }
    
    /**
     * {@inheritdoc}
     */
    public function loadCache()
    {
        if ($this->loaded)
        {
            return $this->cacheLoaded();
        }
        
        $this->loaded = true;
        
        if (file_exists($this->getCacheFile()))
        {
            $loader = LoaderFactory::create($this->getCacheFile());
            $this->cachedata = $loader->load($this->getCacheFile());
            
            if (isset($this->cachedata['_version']) && $this->cachedata['_version'] !== $this->cacheVersion)
            {
                $this->cachedata = null;
                $this->build = true;
                
                return false;
            }
            
            $this->config->merge($this->cachedata);
            return true;
        } 
        else
        {
            $this->build = true;
            return false;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function cacheLoaded()
    {
        if (!$this->loaded)
        {
            $this->loadCache();
        }
        
        return null !== $this->cachedata;
    }
    
    /**
     * {@inheritdoc}
     */
    public function loadFromCache($file, array $options = []) 
    {
        return $this->cacheLoaded();
    }
    
    /**
     * {@inheritdoc}
     */
    public function exportToCache() 
    {
        if ($this->cachedata && count(array_diff_key($this->config->all(), $this->cachedata)) > 0)
        {
            $this->build = true;
        }
        
        if ($this->build)
        {
            $this->build = false;
            
            if (null === $this->writer)
            {
                $this->writer = WriterFactory::create($this->getCacheFile());
            }
            
            $writed = $this->writer->export(['_version' => $this->cacheVersion] + $this->config->all(), $this->getCacheFile());
            return $writed;
        }
        
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function invalidateCache()
    {
        $this->cachedata = null;
        $this->build = false;
        
        if (file_exists($this->getCacheFile()))
        {
            unlink($this->getCacheFile());
        }
        
        return true;
    }
    
    /**
     * @return string
     */
    protected function getCacheFile() 
    {
        return $this->path . DIRECTORY_SEPARATOR . $this->file;
    }
}
