<?php

namespace Elixir\Config\Writer;

use Elixir\Config\Writer\ArrayWriter;
use Elixir\Config\Writer\JSONWriter;
use Elixir\Config\Writer\SerializedWriter;
use Elixir\Config\Writer\WriterInterface;
use Elixir\Config\Writer\YAMLWriter;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class WriterFactory 
{
    /**
     * @param WriterFactory $factory
     */
    public static function addProvider(self $factory)
    {
        $factory->add('array', function($file, $options)
        {
            if (strstr($file, '.php'))
            {
                return new ArrayWriter();
            }
            
            return null;
        });
        
        $factory->add('JSON', function($file, $options)
        {
            if (strstr($file, '.json'))
            {
                return new JSONWriter();
            }

            return null;
        });
        
        $factory->add('YAML', function($file, $options)
        {
            if (strstr($file, '.yml'))
            {
                return new YAMLWriter();
            }

            return null;
        });
        
        $factory->add('serialized', function($file, $options)
        {
            if (strstr($file, '.cache'))
            {
                return new SerializedWriter();
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
     * @param string $file
     * @param array $options
     * @return WriterInterface
     * @throws \InvalidArgumentException
     */
    public function create($file, array $options = []) 
    {
        foreach($this->$factories as $loader)
        {
            $result = $loader($file, $options);
            
            if (null !== $result)
            {
                return $result;
            }
        }
        
        throw new \InvalidArgumentException('No writer has been implemented for this type of resource.');
    }
}
