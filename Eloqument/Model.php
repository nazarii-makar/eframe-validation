<?php

namespace EFrame\Database\Eloquent;

use EFrame\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model as IlluminateModel;

abstract class Model extends IlluminateModel
{
    /**
     * The variants of number to return for pagination.
     *
     * @var int
     */
    protected $limits = [
        1, 5, 10, 15, 20, 25, 50,
    ];

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 25;

    /**
     * The attributes that sorting.
     *
     * @var array
     */
    protected $sortable = [];

    /**
     * Model constructor.
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct(collect($attributes)->toArray());
    }

    /**
     * Get the variants of number to return per page.
     *
     * @return int
     */
    public function getLimits()
    {
        return $this->limits;
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \EFrame\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * @return \EFrame\Database\Query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        return new QueryBuilder(
            $connection, $connection->getQueryGrammar(), $connection->getPostProcessor()
        );
    }
}
