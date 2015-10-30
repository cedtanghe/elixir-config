<?php

namespace Elixir\Config\Writer;

use Elixir\Config\Writer\WriterAbstract;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class JSON extends WriterAbstract 
{
    /**
     * {@inheritdoc}
     */
    public function write() 
    {
        return json_encode($this->config->all(), JSON_PRETTY_PRINT);
    }

    /**
     * {@inheritdoc}
     */
    public function export($file)
    {
        if (!strstr($file, '.json'))
        {
            $file .= '.json';
        }
        
        file_put_contents($file, $this->write());
        return file_exists($file);
    }
}
