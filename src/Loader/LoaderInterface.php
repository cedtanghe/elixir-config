<?php

namespace Elixir\Config\Loader;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface LoaderInterface 
{
    /**
     * @var string
     */
    const RESOURCES = '@resources';
    
    /**
     * @param mixed $config
     * @return array
     */
    public function load($config);
}
