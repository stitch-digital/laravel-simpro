<?php

namespace StitchDigital\Simpro;

use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\PagedPaginator;
use Saloon\PaginationPlugin\Contracts\HasPagination;
use Illuminate\Support\Facades\Cache;
use Saloon\RateLimitPlugin\Contracts\RateLimitStore;
use Saloon\RateLimitPlugin\Traits\HasRateLimits;
use Saloon\RateLimitPlugin\Stores\LaravelCacheStore;
use Saloon\RateLimitPlugin\Limit;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;
use StitchDigital\Simpro\Resources\Customer;

class Simpro extends Connector implements HasPagination
{
    use AcceptsJson;
    use AlwaysThrowOnErrors;
    use HasRateLimits;

    /*
    |--------------------------------------------------------------------------
    | Connector Configuration
    |--------------------------------------------------------------------------
    |
    | Configures the connector with the necessary settings to interact with the
    | Simpro API. This includes the base URL, default headers, authentication,
    | and rate limits.
    |
    */

    public function resolveBaseUrl(): string
    {
        return config('simpro.base_url');
    }

    /**
     * Provide the default headers for every request.
     *
     * @return array
     */
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Provide the default authentication method for every request.
     *
     * @return TokenAuthenticator
     */
    protected function defaultAuth(): TokenAuthenticator
    {
        return new TokenAuthenticator(config('simpro.api_key'));
    }

    /**
     * Paginate the given request.
     *
     * @param Request $request
     * @return PagedPaginator
     */
    public function paginate(Request $request): PagedPaginator
    {
        return new class($this, request: $request) extends PagedPaginator
        {
            /**
             * Determine if the current page is the last page.
             *
             * @param Response $response
             * @return bool
             */
            protected function isLastPage(Response $response): bool
            {
                $currentPage = (int)$response->getRequest()->query()->get('page', 1);
                $totalPages = (int)$response->header('Result-Pages');
                return $currentPage >= $totalPages;
            }

            /**
             * Get the items from the current page.
             *
             * @param Response $response
             * @param Request $request
             * @return array
             */
            protected function getPageItems(Response $response, Request $request): array
            {
                return $response->json('items');
            }
        };
    }

    /**
     * Resolve the rate limits from the configuration.
     *
     * @return array
     */
    protected function resolveLimits(): array
    {
        return [
            Limit::allow(config('simpro.rate_limit.per_second'), threshold: config('simpro.rate_limit.threshold'))->everySeconds(seconds: 1)->sleep(),
        ];
    }

    /**
     * Resolve the rate limit store from the configuration.
     *
     * @return RateLimitStore
     */
    protected function resolveRateLimitStore(): RateLimitStore
    {
        return new LaravelCacheStore(Cache::store(config('simpro.cache.driver')));
    }

    /**
     * Get the prefix for the rate limiter.
     * This prefix is used to store the rate limit data in the cache.
     * Simpro Rate Limits are associated to each client build URL
     * endpoint, so we use the base URL as the prefix.
     *
     * @return string|null
     */
    protected function getLimiterPrefix(): ?string
    {
        return 'simpro-build-' . $this->resolveBaseUrl();
    }

    /*
    |--------------------------------------------------------------------------
    | Resources
    |--------------------------------------------------------------------------
    |
    | Define the resources available for the Simpro API. Each resource is
    | a collection of related requests that can be used to interact with
    | the API and implement chained methods for building requests.
    |
    */

    public function customers(): Customer
    {
        return new Customer($this);
    }
}
