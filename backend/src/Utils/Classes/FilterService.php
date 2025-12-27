<?php
/**
 * Servicio para controlar y gestionar los filtros y orden de un listado
 *
 */

namespace App\Utils\Classes;

use App\Request\FilterRequest;
use Symfony\Component\HttpFoundation\Request;

class FilterService
{
    public array $order = [];
    public array $filters = [];
    public int $page = 1;
    public int $limit = 25;

    /**
     * Esta propiedad solo se rellenará en el caso de que el sistema de orden esté contenido en una variable única llamada current_request,
     * dentro de la petición que se realiza en el momento.
     * @var string
     *
     *
     */
    public mixed $currentRequest = null;

    private string $stringPath;
    private array $queryParameters;
    private Request $request;

public function __construct(FilterRequest $request)
    {

        $this->order = @$request->getAttribute('filter_order') ?: [];
        $this->currentRequest = @$request->getAttribute('current_request') ?: null;
        $this->filters = @$request->getAttribute('filter_filters') ?: [];
        $this->page = @$request->getAttribute('page') ?: 1;
        $this->limit = @$request->getAttribute('limit') ?: 25;
        $this->request = $request->getRequest();

        $pathPaterns = explode("?", $this->request->getUri());

        $this->queryParameters = $request->getRequest()->query->all();
        unset($this->queryParameters['filter_order']);
        unset($this->queryParameters['current_request']);
        unset($this->queryParameters['__page']);
        unset($this->queryParameters['__limit']);

        $this->stringPath = $pathPaterns[0];

    }

    public function getRequest(): ?Request
    {
        return $this->request;
    }


    /**
     * Genera el link para cambiar un orden del filtro.
     * @param $field
     * @param $order
     * @return string
     */
    public function orderBy($field, $order): string
    {
        $completeRequest = $this->futureOrderRequest($field, $order);
        // Simulamos como quedaría el array después de añadirle el orden
        return $this->getCompletePath($completeRequest);
    }

    /**
     * recibe un array con los parámetros que hay que generar la query del enlace
     * @param $completeRequest
     * @return string
     */
    public function getCompletePath($completeRequest): string
    {
        return $this->getPath() . "?" . $this->buildQuery($completeRequest);
    }

    /**
     * @return string
     */
    private function getPath(): string
    {
        return $this->stringPath;
    }

    /**
     * Funcion que hace un stringify de los parámetros
     * para devolver un string con la query
     * @param $param
     * @return string
     */
    public function buildQuery($param): string
    {

        $params = array_merge($param, $this->queryParameters);
        return http_build_query($params);
    }

    public function limitBy($newLimit): string
    {
        $completeRequest = $this->getCurrentRequestParams();
        $completeRequest["__limit"] = $newLimit < 0 ? 50 : $newLimit;
        $completeRequest["__page"] = 1;
        return $this->getCompletePath($completeRequest);
    }


    /**
     * Devuelve el array con los parámtros de la query actual.
     * @return array
    */
    public function getAll(): array
    {
        return [
            "filter_order" => $this->order,
            "filter_filters" => $this->filters,
            "__limit" => $this->limit,
            "__page" => $this->page,
        ];
    }

    public function getAllOrders()
    {
        return $this->order;
    }

    private function addOrder($field, $order, &$currentRequest): void
    {
        $exist = false;
        foreach ($currentRequest["filter_order"] as $index => $orderField) {
            if ($orderField["field"] == $field) {
                $exist = true;
                $currentRequest["filter_order"][$index]["order"] = $order;
            }
        }
        if (!$exist) {
            $currentRequest["filter_order"] = [];

            $currentRequest["filter_order"][] = [
                "field" => $field,
                "order" => $order
            ];
        }
    }

    public function addOrderValue($field, $order): void
    {
        $this->order[] = ["field" => $field, 'order' => $order];
    }

    public function getCurrentRequestParams(): array
    {
        return [
            "filter_order" => $this->order,
            "filter_filters" => $this->filters,
            "__limit" => $this->limit,
            "__page" => $this->page,
        ];
    }

    public function addFilter($filterName, $value): static
    {
        $this->filters[$filterName] =  $value;
        return $this;
    }


    public function getCurrentRequest()
    {
        return $this->currentRequest;
    }

    /**
     * Recupera un order específico si existe en los ordenes establecidos
     * @param $fieldName
     * @return bool|array
     */
    public function getOrder($fieldName): bool|array|string
    {
        foreach ($this->order as $orderField) {
            if ($orderField["field"] == $fieldName) {
                return $orderField['order'];
            }
        }
        return false;
    }


    /**
     * Comprueba si en la request existe este campo ordenado.
     * @param $fieldName
     * @return bool
     */
    public function isOrdered($fieldName): bool
    {
        $parameters = $this->getAll();
        if ($parameters and isset($parameters['filter_order']) and count($parameters['filter_order']) > 0) {
            foreach ($parameters['filter_order'] as $order) {
                if ($order["field"] == $fieldName) return true;
            }
        }
        return false;
    }

    /**
     * Comprueba si ya existe un orden para ese campo y retorna el orden contrario,
     * Se puede utilizar para mostrar los iconos ordenar en una dirección y otra.
     */
    public function getInversedOrder($fieldName): string
    {
        $order = $this->getOrder($fieldName);

        if ($order) {
            if ($order == "DESC") return "ASC";
            if ($order == "ASC") return "DESC";
        }
        return "ASC";
    }

    public function getFilters(){
        return $this->filters;
    }

    public function getOrders(){

        $orders = [];
        foreach($this->order as $order){
            $orders[$order['field']] = strtoupper($order['order']);
        }

        return $orders;
    }


    /**
     * Coge los parámetros actuales de la petición y modifica el orden para
     * que se pueda generar un link con el orden cambiado
     * @param $fieldName
     * @param $order
     * @return array
     */
    public function futureOrderRequest($fieldName, $order): array
    {
        $currentRequest = $this->getCurrentRequestParams();
        $this->addOrder($fieldName,$order, $currentRequest);
        return $currentRequest;
    }

    public function renderFieldName($fieldName): string
    {
        return "filter_filters[$fieldName]";
    }

    public function filterFormField(): string
    {
        return '<input type="hidden" name="current_request" value="'.$this->getCurrentRequest().'">';
    }


    /**
     * Recupera el valor de un campo filtrado
     * @param $fieldName
     * @return mixed
     */
    public function getFilter($fieldName): mixed
    {
        foreach ($this->filters as $indexName => $filter) {
            if($indexName == $fieldName) {
                return $filter;
            }
        }
        return null;
    }

    public function hasFilter(string $filterName): bool
    {
        return isset($this->filters[$filterName]);
    }


    public function pageBy($page): string
    {
        $completeRequest = $this->getCurrentRequestParams();
        $completeRequest["__page"] = $page < 0 ? 1 : $page;
        return $this->getCompletePath($completeRequest);
    }

}
