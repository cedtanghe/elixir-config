<?php

namespace Elixir\Config\Loader;

use Elixir\Config\Loader\ArrayLoader;
use Elixir\Config\Loader\JSONLoader;
use Elixir\Config\Loader\LoaderInterface;
use Elixir\Config\Loader\SerializedLoader;
use Elixir\Config\Loader\YAMLLoader;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class LoaderFactory 
{
    /**
     * @param LoaderFactory $factory
     */
    public static function addProvider(self $factory)
    {
        $factory->add('array', function($config, $options)
        {
            if (is_array($config) || strstr($config, '.php'))
            {
                $options['environment'] = isset($options['environment']) ? $options['environment'] : null;
                $options['strict'] = isset($options['strict']) ? $options['strict'] : false;
                
                return new ArrayLoader($options['environment'], $options['strict']);
            }
            
            return null;
        });
        
        $factory->add('JSON', function($config, $options)
        {
            if (strstr($config, '.json'))
            {
                $options['environment'] = isset($options['environment']) ? $options['environment'] : null;
                $options['strict'] = isset($options['strict']) ? $options['strict'] : false;
                
                return new JSONLoader($options['environment'], $options['strict']);
            }

            return null;
        });
        
        $factory->add('YAML', function($config, $options)
        {
            if (strstr($config, '.yml'))
            {
                $options['environment'] = isset($options['environment']) ? $options['environment'] : null;
                $options['strict'] = isset($options['strict']) ? $options['strict'] : false;
            
                return new YAMLLoader($options['environment'], $options['strict']);
            }

            return null;
        });
        
        $factory->add('serialized', function($config, $options)
        {
            if (strstr($config, '.cache'))
            {
                $options['environment'] = isset($options['environment']) ? $options['environment'] : null;
                $options['strict'] = isset($options['strict']) ? $options['strict'] : false;
            
                return new SerializedLoader($options['environment'], $options['strict']);
            }
            
            return null;
        });
    }
    
    /**
     * @var array 
     */
    protected $factories = [];
    
    /**
     * @param string $key
     * @return boolean
     */
    public function has($key)
    {
        return isset($this->factories[$key]);
    }

    /**
     * @param string $key
     * @param callable $resolver
     */
    public function add($key, callable $resolver)
    {
        $this->factories[$key] = $resolver;
    }

    /**
     * @param mixed $config
     * @param array $options
     * @return LoaderInterface
     * @throws \InvalidArgumentException
     */
    public function create($config, array $options = []) 
    {
        foreach($this->factories as $loader)
        {
            $result = $loader($config, $options);
            
            if (null !== $result)
            {
                return $result;
            }
        }
        
        throw new \InvalidArgumentException('No loader has been implemented for this type of resource.');
    }
}
