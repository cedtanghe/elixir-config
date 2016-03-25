<?php

namespace Elixir\Config\Loader;

use Elixir\Config\Loader\ArrayLoader;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class JSONLoader extends ArrayLoader 
{
    /**
     * {@inheritdoc}
     */
    public function load($config)
    {
        if (is_file($config)) 
        {
            $config = file_get_contents($config);
        }
        
        return parent::load(json_decode($config, true));
    }
}
