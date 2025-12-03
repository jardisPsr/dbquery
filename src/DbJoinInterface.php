<?php

declare(strict_types=1);

namespace JardisPsr\DbQuery;

use InvalidArgumentException;

/**
 * Interface for basic JOIN operations in SQL query builders.
 *
 * This interface provides methods for INNER JOIN and LEFT JOIN operations
 * that are commonly supported across SELECT, UPDATE, and DELETE queries.
 * It implements the Interface Segregation Principle by isolating basic
 * JOIN functionality that works across different SQL statement types.
 *
 * Note: More advanced JOIN types (RIGHT JOIN, FULL JOIN, CROSS JOIN) are
 * available only in SELECT queries via DbQueryBuilderInterface.
 *
 * @package JardisPsr\Contract\Database
 */
interface DbJoinInterface
{
    /**
     * Adds an INNER JOIN clause to the query.
     *
     * Joins another table or subquery using INNER JOIN, returning only rows
     * where the join condition matches in both tables. This is the most common
     * type of join and is supported across SELECT, UPDATE (MySQL/MariaDB), and
     * DELETE (MySQL/MariaDB) operations.
     *
     * When using a subquery, an alias is required and will be enforced by the implementation.
     *
     * Examples:
     * ```php
     * // Join with table
     * $query->innerJoin('orders', 'orders.user_id = users.id', 'o');
     *
     * // Join with subquery
     * $subquery = $builder->select('id, total')->from('orders')->where('status')->equals('completed');
     * $query->innerJoin($subquery, 'subq.user_id = users.id', 'subq');
     *
     * // UPDATE with JOIN (MySQL/MariaDB)
     * $update->table('users')
     *        ->innerJoin('banned_ips', 'users.ip = banned_ips.ip')
     *        ->set('status', 'banned')
     *        ->where('banned_ips.permanent')->equals(1);
     * ```
     *
     * @param string|DbQueryBuilderInterface $container The table name or subquery to join
     * @param string $constraint The join condition (e.g., 'orders.user_id = users.id')
     * @param string|null $alias Optional alias for the joined table (required for subqueries)
     * @return self Returns the query builder instance for method chaining
     * @throws InvalidArgumentException If $container is a subquery and $alias is null (enforced in implementation)
     */
    public function innerJoin(
        string|DbQueryBuilderInterface $container,
        string $constraint,
        ?string $alias = null
    ): self;

    /**
     * Adds a LEFT JOIN clause to the query.
     *
     * Joins another table or subquery using LEFT JOIN (LEFT OUTER JOIN), returning
     * all rows from the left table and matching rows from the right table. If no
     * match exists, NULL values are returned for the right table's columns.
     *
     * Supported across SELECT queries and in UPDATE/DELETE for MySQL/MariaDB.
     * When using a subquery, an alias is required and will be enforced by the implementation.
     *
     * Examples:
     * ```php
     * // Join with table
     * $query->from('users')
     *       ->leftJoin('orders', 'orders.user_id = users.id', 'o')
     *       ->select('users.*, COUNT(o.id) as order_count');
     *
     * // Join with subquery
     * $recentOrders = $builder->select('user_id, MAX(created_at) as last_order')
     *                         ->from('orders')
     *                         ->groupBy('user_id');
     * $query->from('users')
     *       ->leftJoin($recentOrders, 'ro.user_id = users.id', 'ro');
     *
     * // UPDATE with LEFT JOIN (MySQL/MariaDB)
     * // Note: Complex expressions in SET clauses require implementation-specific handling
     * $update->table('users')
     *        ->leftJoin('orders', 'orders.user_id = users.id')
     *        ->set('has_orders', 1);
     * ```
     *
     * @param string|DbQueryBuilderInterface $container The table name or subquery to join
     * @param string $constraint The join condition (e.g., 'orders.user_id = users.id')
     * @param string|null $alias Optional alias for the joined table (required for subqueries)
     * @return self Returns the query builder instance for method chaining
     * @throws InvalidArgumentException If $container is a subquery and $alias is null (enforced in implementation)
     */
    public function leftJoin(
        string|DbQueryBuilderInterface $container,
        string $constraint,
        ?string $alias = null
    ): self;
}
