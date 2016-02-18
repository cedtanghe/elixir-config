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
     * @return boolean
     */
    public function cacheLoaded();
    
    /**
     * @return boolean
     */
    public function isFreshCache();
    
    /**
     * @param array $data
     * @return boolean
     */
    public function exportToCache(array $data = null);
    
    /**
     * @return boolean
     */
    public function invalidateCache();
}
