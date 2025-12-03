<?php

declare(strict_types=1);

namespace JardisPsr\DbQuery;

/**
 * Interface for building SQL window function specifications.
 *
 * Window functions perform calculations across a set of table rows that are
 * related to the current row. This interface provides a fluent API for defining
 * the OVER clause including PARTITION BY, ORDER BY, and frame specifications.
 *
 * Window functions are supported in:
 * - MySQL 8.0+
 * - MariaDB 10.2+
 * - PostgreSQL 8.4+
 * - SQLite 3.25.0+
 *
 * Basic syntax: function_name(...) OVER (window_spec)
 *
 * Examples:
 * ```php
 * // ROW_NUMBER() OVER (PARTITION BY department ORDER BY salary DESC)
 * $query->selectWindow('ROW_NUMBER', 'row_num')
 *       ->partitionBy('department')
 *       ->windowOrderBy('salary', 'DESC')
 *       ->endWindow();
 *
 * // SUM(amount) OVER (PARTITION BY customer_id ORDER BY order_date)
 * $query->selectWindow('SUM', 'running_total', 'amount')
 *       ->partitionBy('customer_id')
 *       ->windowOrderBy('order_date')
 *       ->endWindow();
 *
 * // RANK() OVER (ORDER BY score DESC)
 * $query->selectWindow('RANK', 'rank')
 *       ->windowOrderBy('score', 'DESC')
 *       ->endWindow();
 * ```
 *
 * @package JardisPsr\Contract\Database
 */
interface DbWindowBuilderInterface
{
    /**
     * Defines the PARTITION BY clause for the window function.
     *
     * Divides the result set into partitions (groups) to which the window
     * function is applied independently. Without PARTITION BY, the function
     * treats all rows as a single partition.
     *
     * Multiple columns can be specified to create more granular partitions.
     *
     * Examples:
     * ```php
     * // Partition by single column
     * ->partitionBy('department')
     * // SQL: PARTITION BY department
     *
     * // Partition by multiple columns
     * ->partitionBy('department', 'location')
     * // SQL: PARTITION BY department, location
     *
     * // No partition (operates on entire result set)
     * ->windowOrderBy('salary', 'DESC')
     * // SQL: ORDER BY salary DESC
     * ```
     *
     * @param string ...$fields One or more column names to partition by
     * @return self Returns the window builder instance for method chaining
     */
    public function partitionBy(string ...$fields): self;

    /**
     * Defines the ORDER BY clause within the window specification.
     *
     * Determines the order of rows within each partition for window function
     * calculations. Can be called multiple times to sort by multiple columns.
     * The order affects functions like ROW_NUMBER, RANK, LAG, LEAD, and
     * cumulative aggregates.
     *
     * Note: This is separate from the query's main ORDER BY clause.
     *
     * Examples:
     * ```php
     * // Single column ordering
     * ->windowOrderBy('salary', 'DESC')
     * // SQL: ORDER BY salary DESC
     *
     * // Multiple columns (call multiple times)
     * ->windowOrderBy('department', 'ASC')
     * ->windowOrderBy('salary', 'DESC')
     * // SQL: ORDER BY department ASC, salary DESC
     *
     * // Default direction is ASC
     * ->windowOrderBy('hire_date')
     * // SQL: ORDER BY hire_date ASC
     * ```
     *
     * @param string $field The column name to order by
     * @param string $direction The sort direction: 'ASC' (ascending) or 'DESC' (descending), default is 'ASC'
     * @return self Returns the window builder instance for method chaining
     */
    public function windowOrderBy(string $field, string $direction = 'ASC'): self;

    /**
     * Defines a frame specification for the window function.
     *
     * Frame clauses restrict the set of rows within the partition that are
     * considered by the window function. This is typically used with aggregate
     * window functions like SUM, AVG, MIN, MAX to create moving averages,
     * running totals, etc.
     *
     * Frame types:
     * - ROWS: Physical offset from current row
     * - RANGE: Logical offset based on ORDER BY values
     * - GROUPS: Based on peer groups (PostgreSQL only)
     *
     * Common frame specifications:
     * - UNBOUNDED PRECEDING: Start of partition
     * - CURRENT ROW: Current row
     * - UNBOUNDED FOLLOWING: End of partition
     * - N PRECEDING: N rows/values before current
     * - N FOLLOWING: N rows/values after current
     *
     * Note: Frame specifications have varying support across databases:
     * - PostgreSQL: Full support for ROWS, RANGE, GROUPS
     * - MySQL/MariaDB: ROWS and RANGE supported
     * - SQLite: Basic ROWS and RANGE support
     *
     * Examples:
     * ```php
     * // Moving average of last 3 rows including current
     * ->frame('ROWS', '2 PRECEDING', 'CURRENT ROW')
     * // SQL: ROWS BETWEEN 2 PRECEDING AND CURRENT ROW
     *
     * // Running total from start of partition to current row
     * ->frame('ROWS', 'UNBOUNDED PRECEDING', 'CURRENT ROW')
     * // SQL: ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
     *
     * // Centered moving average (1 before, current, 1 after)
     * ->frame('ROWS', '1 PRECEDING', '1 FOLLOWING')
     * // SQL: ROWS BETWEEN 1 PRECEDING AND 1 FOLLOWING
     *
     * // All rows in partition
     * ->frame('RANGE', 'UNBOUNDED PRECEDING', 'UNBOUNDED FOLLOWING')
     * // SQL: RANGE BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING
     * ```
     *
     * @param string $type Frame type: 'ROWS', 'RANGE', or 'GROUPS'
     * @param string $start Frame start boundary (e.g., 'UNBOUNDED PRECEDING', '2 PRECEDING', 'CURRENT ROW')
     * @param string $end Frame end boundary (e.g., 'CURRENT ROW', '1 FOLLOWING', 'UNBOUNDED FOLLOWING')
     * @return self Returns the window builder instance for method chaining
     */
    public function frame(string $type, string $start, string $end): self;

    /**
     * Completes the window specification and returns to the query builder.
     *
     * This method finalizes the window function definition and allows you to
     * continue building the main query with additional SELECT columns, JOINs,
     * WHERE clauses, etc.
     *
     * Example:
     * ```php
     * $query->select('id, name, department')
     *       ->selectWindow('ROW_NUMBER', 'row_num')
     *           ->partitionBy('department')
     *           ->windowOrderBy('salary', 'DESC')
     *           ->endWindow()
     *       ->from('employees')
     *       ->where('active')->equals(1);
     * ```
     *
     * @return DbQueryBuilderInterface Returns the main query builder for method chaining
     */
    public function endWindow(): DbQueryBuilderInterface;
}
