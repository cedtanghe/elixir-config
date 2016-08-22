<?php

namespace Elixir\Config\Loader;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class ArrayLoader implements LoaderInterface
{
    /**
     * @var string
     */
    protected $environment;

    /**
     * @var bool
     */
    protected $strict;

    /**
     * @param string $environment
     * @param bool   $strict
     */
    public function __construct($environment = null, $strict = false)
    {
        $this->environment = $environment;
        $this->strict = $strict;
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @return bool
     */
    public function isStrict()
    {
        return $this->strict;
    }

    /**
     * {@inheritdoc}
     */
    public function load($config)
    {
        if (!is_array($config)) {
            $config = include $config;
        }

        $result = [];
        $supers = [];

        $m = $this->environment;

        if (null !== $m) {
            $found = false;

            do {
                foreach ($config as $key => $value) {
                    $k = explode('>', $key);

                    if (trim($k[0]) === $m) {
                        $found = true;
                        $supers[] = $value;

                        if (isset($k[1])) {
                            $m = trim($k[1]);
                            continue 2;
                        }
                    }
                }

                $m = null;
            } while (null !== $m);

            if (!$found && !$this->strict) {
                $supers[] = $config;
            }
        } else {
            $supers[] = $config;
        }

        foreach (array_reverse($supers) as $data) {
            $data = $this->parse($data);
            $result = array_merge($result, $data);
        }

        return $result;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function parse(array $data)
    {
        if (isset($data[LoaderInterface::RESOURCES])) {
            // Todo
        }

        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                $value = $this->parse($value);
            }
        }

        return $data;
    }
}
