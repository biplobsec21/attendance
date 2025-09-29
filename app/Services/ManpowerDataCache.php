<?php

namespace App\Services;

class ManpowerDataCache
{
    private static $cachedData = [];

    /**
     * Get cached data for a specific date
     *
     * @param string $date
     * @param mixed $data
     * @return mixed|null
     */
    public static function get($date)
    {
        $key = self::generateCacheKey($date);
        return self::$cachedData[$key] ?? null;
    }

    /**
     * Set cached data for a specific date
     *
     * @param string $date
     * @param mixed $data
     * @return void
     */
    public static function set($date, $data)
    {
        $key = self::generateCacheKey($date);
        self::$cachedData[$key] = $data;
    }

    /**
     * Clear cached data for a specific date or all data
     *
     * @param string|null $date
     * @return void
     */
    public static function clear($date = null)
    {
        if ($date === null) {
            self::$cachedData = [];
        } else {
            $key = self::generateCacheKey($date);
            unset(self::$cachedData[$key]);
        }
    }

    /**
     * Generate a cache key for a specific date
     *
     * @param string $date
     * @return string
     */
    private static function generateCacheKey($date)
    {
        return 'manpower_data_' . md5($date);
    }
}
