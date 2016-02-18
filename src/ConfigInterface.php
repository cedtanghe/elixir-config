<?php

namespace Elixir\Config;

use Elixir\Config\Writer\WriterInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface ConfigInterface extends \ArrayAccess
{
    /**
     * @param mixed $config
     * @param array $options
     */
    public function load($config, array $options = []);
    
    /**
     * @param mixed $key
     * @return boolean
     */
    public function has($key);

    /**
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function set($key, $value);

    /**
     * @param mixed $key
     */
    public function remove($key);

    /**
     * @return array
     */
    public function all();

    /**
     * @param array $data
     */
    public function replace(array $data);
    
    /**
     * @param WriterInterface $writer
     * @param string $file
     * @return boolean
     */
    public function export(WriterInterface $writer, $file);

    /**
     * @param ConfigInterface|array
     */
    public function merge($data);
}
