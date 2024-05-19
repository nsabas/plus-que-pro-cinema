<?php

namespace App\Cache;

use Symfony\Contracts\Cache\CacheInterface;

trait CacheTrait
{
    protected CacheInterface $cache;

    /**
     *
     * Generate cache key
     *
     * @param string $prefix
     * @param array $criteria
     * @return string
     */
    public function generateCacheKey(string $prefix, array $criteria = []): string
    {
        if (empty($criteria)){
            return md5($prefix);
        }

        ksort($criteria);

        return md5($prefix . '_' . http_build_query($criteria));
    }
}
