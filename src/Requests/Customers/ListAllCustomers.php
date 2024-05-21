<?php

namespace StitchDigital\Simpro\Requests\Customers;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class ListAllCustomers extends Request implements Paginatable
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    /**
     * Specifies the default company ID as 0
     */
    public function __construct(protected string $companyID = '0') {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/api/v1.0/companies/' . $this->companyID . '/customers/';
    }
}
