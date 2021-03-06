<?php

namespace Elixir\Config\Cache;

use Elixir\Config\Loader\LoaderFactory;
use Elixir\Config\Loader\LoaderFactoryAwareTrait;
use Elixir\Config\Writer\WriterFactory;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Compiled implements CacheableInterface
{
    use LoaderFactoryAwareTrait;
    use WriterFactoryAwareTrait;

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
     * @var bool
     */
    protected $build = true;

    /**
     * @var bool
     */
    protected $loaded = false;

    /**
     * @var array
     */
    protected $cachedata;

    /**
     * @param string              $path
     * @param string              $file
     * @param string|numeric|null $cacheVersion
     */
    public function __construct($path = null, $file = 'config.cache', $cacheVersion = null)
    {
        $path = $path ?: 'application'.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR;

        if (!is_dir($path)) {
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
    public function loadCache()
    {
        if ($this->cacheLoaded()) {
            return false;
        }

        $this->loaded = true;

        if (file_exists($this->getCacheFile())) {
            if (null === $this->loaderFactory) {
                $this->loaderFactory = new LoaderFactory();
                LoaderFactory::addProvider($this->loaderFactory);
            }

            $loader = $this->loaderFactory->create($this->getCacheFile());
            $this->cachedata = $loader->load($this->getCacheFile());

            if (isset($this->cachedata['_version']) && $this->cachedata['_version'] !== $this->cacheVersion) {
                $this->cachedata = null;
                $this->build = true;

                return false;
            }

            return $this->cachedata;
        } else {
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
        if ($this->build) {
            $this->build = false;

            if (null === $this->writerFactory) {
                $this->writerFactory = new WriterFactory();
                WriterFactory::addProvider($this->writerFactory);
            }

            $writer = $this->writerFactory->create($this->getCacheFile());
            $writed = $writer->export(['_version' => $this->cacheVersion] + $data, $this->getCacheFile());

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

        if (file_exists($this->getCacheFile())) {
            unlink($this->getCacheFile());
        }

        return true;
    }

    /**
     * @return string
     */
    protected function getCacheFile()
    {
        return $this->path.DIRECTORY_SEPARATOR.$this->file;
    }
}
