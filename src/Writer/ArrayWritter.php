<?php

namespace Elixir\Config\Writer;

use Elixir\Config\Writer\WriterInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class ArrayWritter implements WriterInterface
{
    /**
     * {@inheritdoc}
     */
    public function write(array $data) 
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function export(array $data, $file)
    {
        if (!strstr($file, '.php'))
        {
            $file .= '.php';
        }
        
        file_put_contents($file, '<?php return ' . var_export($this->write($data), true) . ';');
        return file_exists($file);
    }
}
