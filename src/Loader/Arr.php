<?php

namespace Elixir\Config\Loader;

use Elixir\Config\Loader\LoaderAbstract;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Arr extends LoaderAbstract 
{
    /**
     * {@inheritdoc}
     */
    public function load($config) 
    {
        if (!is_array($config)) 
        {
            $config = include $config;
        }

        $result = [];
        $supers = [];

        $m = $this->environment;

        if (null !== $m) 
        {
            $found = false;

            do 
            {
                foreach ($config as $key => $value)
                {
                    $k = explode('>', $key);

                    if (trim($k[0]) === $m) 
                    {
                        $found = true;
                        $supers[] = $value;

                        if (isset($k[1])) 
                        {
                            $m = trim($k[1]);
                            continue 2;
                        }
                    }
                }

                $m = null;
            } 
            while (null !== $m);

            if (!$found && !$this->strict) 
            {
                $supers[] = $config;
            }
        } 
        else 
        {
            $supers[] = $config;
        }

        foreach (array_reverse($supers) as $data)
        {
            $data = $this->parse($data);
            $result = array_merge($result, $data);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function parse($data)
    {
        return $data;
    }
}
