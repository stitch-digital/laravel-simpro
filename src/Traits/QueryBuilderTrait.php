<?php

namespace StitchDigital\Simpro\Traits;

use DateTimeInterface;

trait QueryBuilderTrait
{
    /**
     * The query parameters.
     *
     * @var array
     */
    protected array $queryParams = [];

    /**
     * The If-Modified-Since header value.
     *
     * @var ?string
     */
    protected ?string $ifModifiedSince = null;

    /**
     * The columns to be included in the response.
     *
     * @var ?string
     */
    protected ?string $columns = null;

    /**
     * Set the If-Modified-Since header.
     *
     * @param DateTimeInterface $date The date and time of the last modification.
     * @return static Returns the current instance for method chaining.
     */
    public function lastModified(DateTimeInterface $date): static
    {
        // Format the date according to RFC7231 and store it
        $this->ifModifiedSince = $date->format(DateTimeInterface::RFC7231);
        return $this;
    }

    /**
     * Set the columns to be included in the response.
     *
     * @param string $columns Comma-separated list of columns.
     * @return static Returns the current instance for method chaining.
     */
    public function schema(string $columns): static
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Add a where condition to the query.
     *
     * @param string $field The field to filter on.
     * @param string $value The value to compare.
     * @param string $operator The operator to use (default is '=').
     * @return static Returns the current instance for method chaining.
     */
    public function where(string $field, string $value, string $operator = '='): static
    {
        if ($operator === 'like') {
            // Append %25 for wildcard search
            $value = $value . '%25';
        }

        $this->queryParams[$field] = $operator === '=' ? $value : "$operator($value)";
        return $this;
    }

    /**
     * Add a where not condition to the query.
     *
     * @param string $field The field to filter on.
     * @param string $value The value to compare.
     * @param string $operator The operator to use (default is '=').
     * @return static Returns the current instance for method chaining.
     */
    public function whereNot(string $field, string $value, string $operator = '='): static
    {
        $operator = $operator === '=' ? 'ne' : "!$operator";
        $this->queryParams[$field] = "$operator($value)";
        return $this;
    }

    /**
     * Add a where between condition to the query.
     *
     * @param string $field The field to filter on.
     * @param mixed $from The start value.
     * @param mixed $to The end value.
     * @return static Returns the current instance for method chaining.
     */
    public function whereBetween(string $field, mixed $from, mixed $to): static
    {
        // Ensure the values are formatted correctly for dates
        if ($from instanceof DateTimeInterface) {
            $from = $from->format('Y-m-d');
        }
        if ($to instanceof DateTimeInterface) {
            $to = $to->format('Y-m-d');
        }

        $this->queryParams[$field] = "between($from,$to)";
        return $this;
    }

    /**
     * Add an order by condition to the query.
     *
     * @param string $field The field to order by.
     * @param string $direction The direction to order by (asc or desc).
     * @return static Returns the current instance for method chaining.
     */
    public function orderBy(string $field, string $direction = 'asc'): static
    {
        $prefix = $direction === 'desc' ? '-' : '';
        $this->queryParams['orderby'] = "$prefix$field";
        return $this;
    }

    /**
     * Set the limit of records to retrieve.
     *
     * @param int $limit The limit of records.
     * @return static Returns the current instance for method chaining.
     */
    public function limit(int $limit): static
    {
        $this->queryParams['limit'] = $limit;
        return $this;
    }
}
