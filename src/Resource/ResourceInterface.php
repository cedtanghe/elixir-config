<?php

namespace Elixir\Config\Resource;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface ResourceInterface
{
    /**
     * @return mixed
     */
    public function __invoke();
    
    /**
     * @return string
     */
    public function __toString();
}
