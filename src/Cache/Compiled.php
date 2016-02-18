<?php

namespace Elixir\Config\Cache;

use Elixir\Config\Cache\CacheableInterface;
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
    protected $build = true;
    
    /**
     * @var boolean 
     */
    protected $loaded = false;

    /**
     * @var WriterInterface 
     */
    protected $writer;

    /**
     * @var array 
     */
    protected $cachedata;

    /**
     * @param string $path
     * @param string $file
     * @param string|numeric|null $cacheVersion
     */
    public function __construct($path = null, $file = 'config.cache', $cacheVersion = null) 
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
        if ($this->cacheLoaded())
        {
            return false;
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
            
            return $this->cachedata;
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
        return $this->loaded;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isFreshCache()
    {
        return !$this->build;
    }
    
    /**
     * {@inheritdoc}
     */
    public function exportToCache(array $data = null) 
    {
        if ($this->build)
        {
            $this->build = false;
            
            if (null === $this->writer)
            {
                $this->writer = WriterFactory::create($this->getCacheFile());
            }
            
            $writed = $this->writer->export(['_version' => $this->cacheVersion] + $data, $this->getCacheFile());
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
        $this->loaded = false;
        $this->build = true;
        
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
