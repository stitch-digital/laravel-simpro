<?php

namespace StitchDigital\Simpro\Requests\Customers;

use Illuminate\Support\Facades\Cache;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\PaginationPlugin\Contracts\Paginatable;
use Saloon\CachePlugin\Contracts\Driver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;

/**
 * Class ListAllCustomers
 *
 * This class represents a request to list all customers. It implements
 * Pagination and Cacheable interfaces, and uses the HasCaching trait.
 */
class ListAllCustomers extends Request implements Paginatable, Cacheable
{
    use HasCaching;

    /**
     * The HTTP method of the request. In this case, it's a GET request.
     */
    protected Method $method = Method::GET;

    /**
     * Constructor for the ListAllCustomers class.
     *
     * @param string $companyID The ID of the company. Defaults to '0'.
     */
    public function __construct(protected string $companyID = '0')
    {
    }

    /**
     * This method resolves the endpoint for the request.
     *
     * @return string The endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return '/api/v1.0/companies/' . $this->companyID . '/customers/';
    }

    /**
     * This method resolves the cache driver for the request.
     *
     * @return Driver The cache driver for the request.
     */
    public function resolveCacheDriver(): Driver
    {
        return new LaravelCacheDriver(Cache::store(config('simpro.cache.driver')));
    }

    /**
     * This method returns the cache expiry time in seconds.
     *
     * If caching is enabled, it returns the cache expiry time from the
     * configuration. If caching is not enabled, it returns 0.
     *
     * @return int The cache expiry time in seconds.
     */
    public function cacheExpiryInSeconds(): int
    {
        if (config('simpro.cache.enabled')) {
            return config('simpro.cache.expire');
        }

        return 0;
    }
}
