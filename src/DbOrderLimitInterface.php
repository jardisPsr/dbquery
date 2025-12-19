<?php

declare(strict_types=1);

namespace JardisPsr\DbQuery;

/**
 * Interface for ORDER BY and LIMIT clauses in SQL query builders.
 *
 * This interface provides methods for sorting and limiting query results,
 * which are commonly used across SELECT, UPDATE, and DELETE operations.
 * It implements the Interface Segregation Principle by isolating ordering
 * and pagination functionality.
 *
 * Note: LIMIT in UPDATE/DELETE is MySQL/MariaDB specific and not standard SQL.
 * PostgresSQL and SQLite have limited or no support for these features in
 * UPDATE/DELETE statements.
 *
 * @package JardisPsr\Contract\Database
 */
interface DbOrderLimitInterface
{
    /**
     * Specifies the column and direction for sorting results.
     *
     * Defines the ORDER BY clause for sorting query results. Can be called
     * multiple times to sort by multiple columns in sequence (first call has
     * highest priority).
     *
     * In SELECT queries, this determines the order of returned rows.
     * In UPDATE/DELETE queries (MySQL/MariaDB), this is used with LIMIT to
     * control which rows are affected.
     *
     * Examples:
     * ```php
     * // SELECT: Sort users by name ascending
     * $query->select('*')->from('users')->orderBy('name', 'ASC');
     *
     * // SELECT: Multiple sort columns
     * $query->select('*')
     *       ->from('products')
     *       ->orderBy('category', 'ASC')
     *       ->orderBy('price', 'DESC');
     *
     * // UPDATE: Update oldest inactive users first (MySQL/MariaDB)
     * $update->table('users')
     *        ->set('status', 'archived')
     *        ->where('active')->equals(0)
     *        ->orderBy('last_login', 'ASC')
     *        ->limit(100);
     *
     * // DELETE: Delete oldest log entries in batches (MySQL/MariaDB)
     * // Note: Complex date expressions may require subqueries or alternative approaches
     * $oneYearAgo = date('Y-m-d', strtotime('-1 year'));
     * $delete->from('logs')
     *        ->where('created_at')->lower($oneYearAgo)
     *        ->orderBy('created_at', 'ASC')
     *        ->limit(1000);
     * ```
     *
     * @param string $field The column name to sort by
     * @param string $direction The sort direction: 'ASC' (ascending) or 'DESC' (descending), default is 'ASC'
     * @return self Returns the query builder instance for method chaining
     */
    public function orderBy(string $field, string $direction = 'ASC'): self;

    /**
     * Limits the number of rows returned/affected and optionally sets an offset.
     *
     * For SELECT queries: Adds LIMIT and OFFSET clauses for pagination or
     * restricting result size. Both parameters are optional to allow flexible usage.
     *
     * For UPDATE/DELETE queries: Only the $limit parameter is used (MySQL/MariaDB specific).
     * The $offset parameter is ignored in UPDATE/DELETE contexts. Useful for processing
     * records in batches.
     *
     * Examples:
     * ```php
     * // SELECT: Get first 10 users
     * $query->select('*')->from('users')->limit(10);
     *
     * // SELECT: Pagination - skip 20, get next 10
     * $query->select('*')->from('users')->limit(10, 20);
     *
     * // SELECT: Get all rows with offset (no limit)
     * $query->select('*')->from('users')->limit(null, 50);
     *
     * // UPDATE: Update 100 rows at a time (MySQL/MariaDB)
     * $update->table('users')
     *        ->set('migrated', 1)
     *        ->where('migrated')->equals(0)
     *        ->limit(100);
     *
     * // DELETE: Delete in batches of 500 (MySQL/MariaDB)
     * $twoYearsAgo = date('Y-m-d', strtotime('-2 years'));
     * $delete->from('old_logs')
     *        ->where('created_at')->lower($twoYearsAgo)
     *        ->limit(500);
     * ```
     *
     * @param int|null $limit Maximum number of rows to return/affect (null for no limit in SELECT)
     * @param int|null $offset Number of rows to skip before starting to return rows
     *                         (SELECT only, ignored in UPDATE/DELETE)
     * @return self Returns the query builder instance for method chaining
     */
    public function limit(?int $limit = null, ?int $offset = null): self;
}
