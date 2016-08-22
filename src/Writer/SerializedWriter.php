<?php

namespace Elixir\Config\Writer;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class SerializedWriter implements WriterInterface
{
    /**
     * {@inheritdoc}
     */
    public function dump(array $data)
    {
        return serialize($data);
    }

    /**
     * {@inheritdoc}
     */
    public function export(array $data, $file)
    {
        if (!strstr($file, '.cache')) {
            $file .= '.cache';
        }

        file_put_contents($file, $this->dump($data));

        return file_exists($file);
    }
}
