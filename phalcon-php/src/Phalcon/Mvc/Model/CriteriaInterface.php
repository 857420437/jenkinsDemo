<?php

namespace Phalcon\Mvc\Model;

use Phalcon\DiInterface;

/**
 * Phalcon\Mvc\Model\CriteriaInterface initializer
 *
 * @see https://github.com/phalcon/cphalcon/blob/1.2.6/ext/mvc/model/criteriainterface.c
 */
interface CriteriaInterface
{

    /**
     * Set a model on which the query will be executed
     *
     * @param string $modelName
     * @return \Phalcon\Mvc\Model\CriteriaInterface
     */
    public function setModelName($modelName);

    /**
     * Returns an internal model name on which the criteria will be applied
     *
     * @return string
     */
    public function getModelName();

    /**
     * Adds the bind parameter to the criteria
     *
     * @param string $bindParams
     * @param boolean $merge
     * @return \Phalcon\Mvc\Model\CriteriaInterface
     */
    public function bind(array $bindParams, $merge = false);

    /**
     * Sets the bind types in the criteria
     * This method replaces all previously set bound parameters
     *
     * @param string $bindTypes
     * @return \Phalcon\Mvc\Model\Criteria
     */
    public function bindTypes($bindTypes);

    /**
     * Adds the conditions parameter to the criteria
     *
     * @param string $conditions
     * @return \Phalcon\Mvc\Model\CriteriaInterface
     */
    public function where($conditions);

    /**
     * Adds the conditions parameter to the criteria
     *
     * @param string $conditions
     * @return \Phalcon\Mvc\Model\CriteriaInterface
     */
    public function conditions($conditions);

    /**
     * Adds the order-by parameter to the criteria
     *
     * @param string $orderColumns
     * @return \Phalcon\Mvc\Model\CriteriaInterface
     */
    public function orderBy($orderColumns);

    /**
     * Sets the limit parameter to the criteria
     *
     * @param int $limit
     * @param int|null $offset
     * @return \Phalcon\Mvc\Model\CriteriaInterface
     */
    public function limit($limit, $offset = null);

    /**
     * Sets the "for_update" parameter to the criteria
     *
     * @param boolean|null $forUpdate
     * @return \Phalcon\Mvc\Model\CriteriaInterface
     */
    public function forUpdate($forUpdate = null);

    /**
     * Sets the "shared_lock" parameter to the criteria
     *
     * @param boolean|null $sharedLock
     * @return \Phalcon\Mvc\Model\Criteria
     */
    public function sharedLock($sharedLock = null);

    /**
     * Appends a condition to the current conditions using an AND operator
     *
     * @param string $conditions
     * @param array|null $bindParams
     * @param array|null $bindTypes
     * @return \Phalcon\Mvc\Model\Criteria
     */
    public function andWhere($conditions, $bindParams = null, $bindTypes = null);

    /**
     * Appends a condition to the current conditions using an OR operator
     *
     * @param string $conditions
     * @param array|null $bindParams
     * @param array|null $bindTypes
     * @return \Phalcon\Mvc\Model\Criteria
     */
    public function orWhere($conditions, $bindParams = null, $bindTypes = null);

    /**
     * Appends a BETWEEN condition to the current conditions
     *
     * <code>
     *  $builder->betweenWhere('price', 100.25, 200.50);
     * </code>
     *
     * @param string $expr
     * @param mixed $minimum
     * @param mixed $maximum
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public function betweenWhere($expr, $minimum, $maximum);

    /**
     * Appends a NOT BETWEEN condition to the current conditions
     *
     * <code>
     *  $builder->notBetweenWhere('price', 100.25, 200.50);
     * </code>
     *
     * @param string $expr
     * @param mixed $minimum
     * @param mixed $maximum
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public function notBetweenWhere($expr, $minimum, $maximum);

    /**
     * Appends an IN condition to the current conditions
     *
     * <code>
     *  $builder->inWhere('id', [1, 2, 3]);
     * </code>
     *
     * @param string $expr
     * @param array $values
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public function inWhere($expr, $values);

    /**
     * Appends a NOT IN condition to the current conditions
     *
     * <code>
     *  $builder->notInWhere('id', [1, 2, 3]);
     * </code>
     *
     * @param string $expr
     * @param array $values
     * @return \Phalcon\Mvc\Model\Query\Builder
     */
    public function notInWhere($expr, $values);

    /**
     * Returns the conditions parameter in the criteria
     *
     * @return string
     */
    public function getWhere();

    /**
     * Returns the conditions parameter in the criteria
     *
     * @return string
     */
    public function getConditions();

    /**
     * Returns the limit parameter in the criteria
     *
     * @return string
     */
    public function getLimit();

    /**
     * Returns the order parameter in the criteria
     *
     * @return string
     */
    public function getOrderBy();

    /**
     * Returns all the parameters defined in the criteria
     *
     * @return string
     */
    public function getParams();

    /**
     * Executes a find using the parameters built with the criteria
     *
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function execute();
}
