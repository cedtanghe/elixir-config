<?php

namespace Elixir\Config\Loader;

use Elixir\Config\Loader\Arr;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Serialize extends Arr 
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
        
        return parent::load(unserialize($config));
    }
}
