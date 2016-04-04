<?php

namespace Elixir\Config\Writer;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface WriterInterface
{
    /**
     * @param array $data
     * @return mixed
     */
    public function dump(array $data);

    /**
     * @param array $data
     * @param string $file
     * @return boolean
     */
    public function write(array $data, $file);
}
