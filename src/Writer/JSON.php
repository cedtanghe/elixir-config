<?php

namespace Elixir\Config\Writer;

use Elixir\Config\Writer\WriterInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class JSON implements WriterInterface
{
    /**
     * {@inheritdoc}
     */
    public function write(array $data) 
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    /**
     * {@inheritdoc}
     */
    public function export(array $data, $file)
    {
        if (!strstr($file, '.json'))
        {
            $file .= '.json';
        }
        
        file_put_contents($file, $this->write($data));
        return file_exists($file);
    }
}
