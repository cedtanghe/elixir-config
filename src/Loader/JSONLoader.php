<?php

namespace Elixir\Config\Loader;

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
        if (is_file($config)) {
            $config = file_get_contents($config);
        }

        return parent::load(json_decode($config, true));
    }
}
