<?php

namespace App\Utils\Interfaces;

use App\Utils\Classes\FilterService;
use Doctrine\ORM\QueryBuilder;

interface FilteredRepositoryInterface
{
    public function addFilters(FilterService $filter, QueryBuilder $query):void;

    public function addOrders(FilterService $filter, QueryBuilder $query):void;
}