<?php

namespace EFrame\Database\Query;

use EFrame\Pagination\Paginator;
use Illuminate\Container\Container;
use EFrame\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder as IlluminateBuilder;

class Builder extends IlluminateBuilder
{
    /**
     * Set the limit and offset for a given page.
     *
     * @param  int  $page
     * @param  int  $perPage
     * @param  int  $let
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function forPage($page, $perPage = 15, $let = 0)
    {
        return $this->skip((($page - 1) * $perPage) + $let)->take($perPage);
    }

    /**
     * Create a new length-aware paginator instance.
     *
     * @param  \Illuminate\Support\Collection  $items
     * @param  int  $total
     * @param  int  $perPage
     * @param  int  $currentPage
     * @param  array  $options
     * @return \EFrame\Pagination\LengthAwarePaginator
     */
    protected function paginator($items, $total, $perPage, $currentPage, $options)
    {
        return Container::getInstance()->makeWith(LengthAwarePaginator::class, compact(
            'items', 'total', 'perPage', 'currentPage', 'options'
        ));
    }

    /**
     * Create a new simple paginator instance.
     *
     * @param  \Illuminate\Support\Collection  $items
     * @param  int $perPage
     * @param  int $currentPage
     * @param  array  $options
     * @return \EFrame\Pagination\Paginator
     */
    protected function simplePaginator($items, $perPage, $currentPage, $options)
    {
        return Container::getInstance()->makeWith(Paginator::class, compact(
            'items', 'perPage', 'currentPage', 'options'
        ));
    }

    /**
     * Paginate the given query into a simple paginator.
     *
     * @param  int  $perPage
     * @param  array  $columns
     * @param  string  $pageName
     * @param  int|null  $page
     * @param  string  $letName
     * @param  int  $let
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null, $letName = 'let', $let = 0)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $let = $let ?: Paginator::resolveCurrentLet($letName);

        $total = $this->getCountForPagination($columns);

        $results = $total ? $this->forPage($page, $perPage, $let)->get($columns) : collect();

        return $this->paginator($results, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
            'currentLet' => $let,
        ]);
    }

    /**
     * Get a paginator only supporting simple next and previous links.
     *
     * This is more efficient on larger data-sets, etc.
     *
     * @param  int  $perPage
     * @param  array  $columns
     * @param  string  $pageName
     * @param  int|null  $page
     * @param  string  $letName
     * @param  int  $let
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function simplePaginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null, $letName = 'let', $let = 0)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $let = $let ?: Paginator::resolveCurrentLet($letName);

        $this->skip((($page - 1) * $perPage) + $let)->take($perPage + 1);

        return $this->simplePaginator($this->get($columns), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
            'currentLet' => $let,
        ]);
    }
}
