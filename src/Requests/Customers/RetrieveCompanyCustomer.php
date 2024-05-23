<?php
declare(strict_types=1);

namespace StitchDigital\Simpro\Requests\Customers;

use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Contracts\Driver;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class RetrieveCompanyCustomer extends Request implements Cacheable
{
    use HasCaching;

    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    /**
     * Specifies the default company ID as 0
     */
    public function __construct(protected string $customerID, protected string $companyID = '0') {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/api/v1.0/companies/' . $this->companyID . '/customers/companies/'. $this->customerID;
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
