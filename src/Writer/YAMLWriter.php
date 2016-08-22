<?php

namespace Elixir\Config\Writer;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class YAMLWriter implements WriterInterface
{
    /**
     * @var callable
     */
    protected $YAMLEncoder;

    /**
     * @param callable $YAMLEncoder
     */
    public function __construct(callable $YAMLEncoder = null)
    {
        if (null !== $YAMLEncoder) {
            $this->setYAMLEncoder($YAMLEncoder);
        } else {
            if (function_exists('yaml_emit')) {
                $this->setYAMLEncoder('yaml_emit');
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
    public function dump(array $data)
    {
        return call_user_func($this->getYAMLEncoder(), $data);
    }

    /**
     * {@inheritdoc}
     */
    public function export(array $data, $file)
    {
        if (!strstr($file, '.yml')) {
            $file .= '.yml';
        }

        file_put_contents($file, $this->dump($data));

        return file_exists($file);
    }
}
