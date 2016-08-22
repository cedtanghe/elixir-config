<?php

namespace Elixir\Config\Resource;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface ResourceInterface
{
    /**
     * @param mixed $context
     *
     * @return array
     */
    public function __invoke($context);
}
