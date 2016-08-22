<?php

namespace Elixir\Config\Writer;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class JSONWriter implements WriterInterface
{
    /**
     * {@inheritdoc}
     */
    public function dump(array $data)
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    /**
     * {@inheritdoc}
     */
    public function export(array $data, $file)
    {
        if (!strstr($file, '.json')) {
            $file .= '.json';
        }

        file_put_contents($file, $this->dump($data));

        return file_exists($file);
    }
}
