<?php

namespace Elixir\Config\Loader;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class YAMLLoader extends ArrayLoader
{
    /**
     * @var callable
     */
    protected $YAMLEncoder;

    /**
     * {@inheritdoc}
     *
     * @param callable $YAMLEncoder
     */
    public function __construct($environment = null, $strict = false, callable $YAMLEncoder = null)
    {
        parent::__construct($environment, $strict);

        if (null !== $YAMLEncoder) {
            $this->setYAMLEncoder($YAMLEncoder);
        } else {
            if (function_exists('yaml_parse')) {
                $this->setYAMLEncoder('yaml_parse');
            }
        }
    }

    /**
     * @param callable $value
     */
    public function setYAMLEncoder(callable $value)
    {
        $this->YAMLEncoder = $value;
    }

    /**
     * @return callable
     */
    public function getYAMLEncoder()
    {
        return $this->YAMLEncoder;
    }

    /**
     * {@inheritdoc}
     */
    public function load($config)
    {
        if (is_file($config)) {
            $config = file_get_contents($config);
        }

        return parent::load(call_user_func($this->getYAMLEncoder(), $config));
    }
}
