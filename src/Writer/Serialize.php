<?php

namespace Elixir\Config\Writer;

use Elixir\Config\Writer\WriterInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Serialize implements WriterInterface
{
    /**
     * {@inheritdoc}
     */
    public function write(array $data) 
    {
        return serialize($data);
    }

    /**
     * {@inheritdoc}
     */
    public function export(array $data, $file)
    {
        if (!strstr($file, '.cache'))
        {
            $file .= '.cache';
        }
        
        file_put_contents($file, $this->write($data));
        return file_exists($file);
    }
}
