<?php
declare(strict_types=1);

namespace StitchDigital\Simpro\Resources;

use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;
use StitchDigital\Simpro\Requests\Customers\ListAllCustomers;
use StitchDigital\Simpro\Requests\Customers\RetrieveCompanyCustomer;
use StitchDigital\Simpro\Requests\Customers\RetrieveCustomerType;
use StitchDigital\Simpro\Requests\Customers\RetrieveIndividualCustomer;
use StitchDigital\Simpro\Traits\QueryBuilderTrait;
use JsonException;
use Exception;

class Customer extends BaseResource
{
    use QueryBuilderTrait;

    /**
     * The company ID.
     *
     * @var string
     */
    protected string $companyID;

    /**
     * Set the company ID.
     *
     * @param string $companyID The ID of the company.
     * @return self Returns the current instance for method chaining.
     */
    public function companyID(string $companyID): self
    {
        $this->companyID = $companyID;
        return $this;
    }

    /**
     * --------------------------------------------------------------------------
     * The ALL method: List all customers.
     * --------------------------------------------------------------------------
     *
     * @param int $page The page number for pagination (default is 1).
     * @return Response The response object containing customer data.
     * @throws FatalRequestException If there is a fatal request error.
     * @throws RequestException If there is a general request error.
     */
    public function all(int $page = 1): Response
    {
        // Create a new ListAllCustomers request with the company ID
        $request = new ListAllCustomers($this->companyID);

        // Add the page query parameter to the request
        $request->query()->add('page', $page);

        // If the If-Modified-Since header is set, add it to the request headers
        if ($this->ifModifiedSince) {
            $request->headers()->add('If-Modified-Since', $this->ifModifiedSince);
        }

        // If columns are specified, add them to the query parameters
        if ($this->columns) {
            $request->query()->add('columns', $this->columns);
        }

        // Add the query parameters to the request
        foreach ($this->queryParams as $key => $value) {
            $request->query()->add($key, $value);
        }

        // Send the request using the connector and return the response
        return $this->connector->send($request);
    }

    /**
     * --------------------------------------------------------------------------
     * The GET method: Get details for a specific customer.
     * --------------------------------------------------------------------------
     *
     * Simpro categories it's customer records as either Company customers
     * or Individual customers. The only difference being that Individual
     * customers have GivenName and FamilyName fields, whereas Company
     * customers do not.
     *
     * The API specifies routes based on the customer type:
     * Company Customers: /api/v1.0/customers/companies/
     * Individual Customers: /api/v1.0/customers/individuals/
     *
     * To make this manageable, the GET method first runs a request
     * to determine which category the customer ID belongs to, and
     * once we have the result, we then run the necessary request.
     *
     * @param string $customerID The ID of the customer.
     * @return Response The response object containing customer details.
     * @throws FatalRequestException If there is a fatal request error.
     * @throws RequestException If there is a general request error.
     * @throws JsonException If there is an error decoding the JSON response.
     * @throws Exception If an unexpected condition occurs.
     */
    public function get(string $customerID): Response
    {
        // Create a new RetrieveCustomerType request with the company ID and customer ID
        $request = new RetrieveCustomerType($customerID, $this->companyID);

        try {
            // Send the request using the connector
            $response = $this->connector->send($request);

            // Check if the response body contains an error with _href
            $body = json_decode($response->body(), true, 512, JSON_THROW_ON_ERROR);

            if (isset($body['error']) && isset($body['_href'])) {
                return $this->sendCorrectRequestBasedOnHref($body['_href'], $customerID);
            }

            // If the response does not contain the expected error, return it as is
            return $response;
        } catch (RequestException $e) {
            $response = $e->getResponse();

            // Check if the error indicates an invalid resource URI
            if ($response->status() === 404 || $response->status() === 400) {
                $error = json_decode($response->body(), true, 512, JSON_THROW_ON_ERROR);

                // Check if the error contains the correct URI
                if (isset($error['_href'])) {
                    return $this->sendCorrectRequestBasedOnHref($error['_href'], $customerID);
                } else {
                    throw new Exception('Invalid customer ID or company ID.');
                }
            }

            // Re-throw the exception if it's not handled
            throw $e;
        }
    }

    /**
     * Send the correct request based on the _href value.
     *
     * @param string $correctUri The correct URI extracted from the error response.
     * @param string $customerID The ID of the customer.
     * @return Response The response object containing customer details.
     * @throws Exception If the _href value is unexpected.
     */
    private function sendCorrectRequestBasedOnHref(string $correctUri, string $customerID): Response
    {
        // Determine if the correct URI is for a company or individual customer
        if (str_contains($correctUri, '/customers/companies/')) {
            $request = new RetrieveCompanyCustomer($customerID, $this->companyID);
        } elseif (str_contains($correctUri, '/customers/individuals/')) {
            $request = new RetrieveIndividualCustomer($customerID, $this->companyID);
        } else {
            throw new Exception('Unexpected _href value in the error response.');
        }

        // Send the correct request and return the response
        return $this->connector->send($request);
    }
}
