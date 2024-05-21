<?php

namespace StitchDigital\Simpro;

use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\PagedPaginator;
use Saloon\PaginationPlugin\Contracts\HasPagination;
use Saloon\Traits\Plugins\AcceptsJson;
use StitchDigital\Simpro\Resources\Customer;

class Simpro extends Connector implements HasPagination
{
    use AcceptsJson;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return config('simpro.base_url');
    }

    /**
     * Default headers for every request
     */
    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    protected function defaultAuth(): TokenAuthenticator
    {
        return new TokenAuthenticator(config('simpro.api_key'));
    }

    public function paginate(Request $request): PagedPaginator
    {
        return new class($this, request: $request) extends PagedPaginator
        {
            protected function isLastPage(Response $response): bool
            {
                $currentPage = (int)$response->getRequest()->query()->get('page', 1);
                $totalPages = (int)$response->header('Result-Pages');
                return $currentPage >= $totalPages;
            }

            protected function getPageItems(Response $response, Request $request): array
            {
                return $response->json('items');
            }
        };
    }

    public function customers(): Customer
    {
        return new Customer($this);
    }
}
