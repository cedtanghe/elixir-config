<?php

namespace Elixir\Config\Loader;

use Elixir\Config\Loader\LoaderFactory;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
trait LoaderFactoryAwareTrait
{
    /**
     * @var LoaderFactory 
     */
    protected $loaderFactory;
    
    /**
     * @param LoaderFactory $value
     */
    public function setLoaderFactory(LoaderFactory $value)
    {
        $this->loaderFactory = $value;
    }
    
    /**
     * @return LoaderFactory
     */
    public function getLoaderFactory()
    {
        return $this->loaderFactory;
    }
}
