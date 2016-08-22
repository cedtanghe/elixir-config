<?php

namespace Elixir\Config\Cache;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface CacheableInterface
{
    /**
     * @return array
     */
    public function loadCache();

    /**
     * @return bool
     */
    public function cacheLoaded();

    /**
     * @return bool
     */
    public function isFreshCache();

    /**
     * @param array $data
     *
     * @return bool
     */
    public function exportToCache(array $data = null);

    /**
     * @return bool
     */
    public function invalidateCache();
}
