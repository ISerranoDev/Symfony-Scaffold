<?php

namespace App\Utils\Classes;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class FilterService
{
    private ?Request $request;
    public array $orders = [];
    public array $filters = [];
    public int $page = 1;
    public int $limit = 25;

    public function __construct(RequestStack $request)
    {
        if(!$request->getCurrentRequest()){return;}
        $this->request = $request->getCurrentRequest();
        $queryRequest = $this->request->query->all();

        $this->orders = @$queryRequest['__orders'] ?: [];
        $this->filters = @$queryRequest['__filters'] ?: [];
        $this->page = @$queryRequest['__page'] ?: 1;
        $this->limit = @$queryRequest['__limit'] ?: 25;

    }

    /**
     * @return Request|null
     */
    public function getRequest(): ?Request
    {
        return $this->request;
    }

    /**
     * @param Request|null $request
     * @return FilterService
     */
    public function setRequest(?Request $request): FilterService
    {
        $this->request = $request;
        return $this;
    }

    public function getOrder(string $key){
        return @$this->orders[$key];
    }

    public function isOrdered(string $key) : bool
    {
        return boolval(@$this->orders[$key]);
    }

    /**
     * @return array
     */
    public function getOrders(): array
    {
        return $this->orders;
    }

    public function getInversedOrder(string $key):string
    {
        if(@$this->orders[$key] == 'ASC'){
            return 'DESC';
        }
        return 'ASC';
    }

    /**
     * @param array $orders
     * @return FilterService
     */
    public function setOrders(array $orders): FilterService
    {
        $this->orders = $orders;
        return $this;
    }

    public function getFilter(string $key){
        return @$this->filters[$key];
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param array $filters
     * @return FilterService
     */
    public function setFilters(array $filters): FilterService
    {
        $this->filters = $filters;
        return $this;
    }

    public function addFilter(string $key, mixed $value): FilterService
    {
        $this->filters[$key] = $value;
        return $this;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $page
     * @return FilterService
     */
    public function setPage(int $page): FilterService
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return FilterService
     */
    public function setLimit(int $limit): FilterService
    {
        $this->limit = $limit;
        return $this;
    }

    public function renderFieldName(string $field): string
    {
        return "__filters[$field]";
    }

    public function orderBy(string $field, string $value): string
    {

        $query = explode("?", $this->getRequest()->getUri());
        $queryStr = $query[0];

        if(@$query[1]){
            $queryStr .= '?' . http_build_query(['__filters' => $this->filters, '__orders' => [$field => $value]]);
        }else{
            $queryStr .= '?' . http_build_query(['__orders' => [$field => $value]]);
        }


        return $queryStr;
    }


}