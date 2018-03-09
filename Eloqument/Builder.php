<?php

namespace EFrame\Database\Eloquent;

use EFrame\Pagination\Paginator;
use Illuminate\Container\Container;
use EFrame\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder as IlluminateBuilder;

class Builder extends IlluminateBuilder
{
    /**
     * Find a model by its primary key or throw an exception.
     *
     * @param  mixed  $id
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail($id, $columns = ['*'])
    {
        try {
            return parent::findOrFail($id, $columns);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw (new ModelNotFoundException)->setModel(
                get_class($this->model), $id
            );
        }
    }

    /**
     * Execute the query and get the first result or throw an exception.
     *
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Model|static
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function firstOrFail($columns = ['*'])
    {
        try {
            return parent::firstOrFail($columns);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw (new ModelNotFoundException)->setModel(get_class($this->model));
        }
    }

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
     * Paginate the given query.
     *
     * @param  int  $perPage
     * @param  array  $columns
     * @param  string  $pageName
     * @param  int|null  $page
     * @param  string  $letName
     * @param  int  $let
     * @param  string  $limitName
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     *
     * @throws \InvalidArgumentException
     */
    public function paginate(
        $perPage = null,
        $columns = ['*'],
        $pageName = 'page',
        $page = null,
        $letName = 'let',
        $let = 0,
        $limitName = 'limit'
    ) {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $let = $let ?: Paginator::resolveCurrentLet($letName);

        $perPage = $perPage ?: Paginator::resolveCurrentLimit($limitName);

        $perPage = (null === $perPage ||
            ! in_array($perPage, $this->model->getLimits())
        ) ? $this->model->getPerPage() : $perPage;

        $results = ($total = $this->toBase()->getCountForPagination())
            ? $this->forPage($page, $perPage, $let)->get($columns)
            : $this->model->newCollection();

        return $this->paginator($results, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
            'currentLet' => $let,
        ]);
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
     * @param  string  $limitName
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function simplePaginate(
        $perPage = null,
        $columns = ['*'],
        $pageName = 'page',
        $page = null,
        $letName = 'let',
        $let = 0,
        $limitName = 'limit'
    ) {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $let = $let ?: Paginator::resolveCurrentLet($letName);

        $perPage = $perPage ?: Paginator::resolveCurrentLimit($limitName);

        $perPage = (null === $perPage ||
            ! in_array($perPage, $this->model->getLimits())
        ) ? $this->model->getPerPage() : $perPage;

        // Next we will set the limit and offset for this query so that when we get the
        // results we get the proper section of results. Then, we'll create the full
        // paginator instances for these results with the given page and per page.
        $this->skip((($page - 1) * $perPage) + $let)->take($perPage + 1);

        return $this->simplePaginator($this->get($columns), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
            'currentLet' => $let,
        ]);
    }
}
