<?php

declare(strict_types=1);

namespace JardisPsr\DbQuery;

use InvalidArgumentException;

/**
 * Interface for building SQL SELECT queries using a fluent interface pattern.
 *
 * This interface provides methods to construct complex SQL queries programmatically,
 * supporting all major SQL clauses including SELECT, FROM, WHERE, JOIN, GROUP BY,
 * HAVING, ORDER BY, LIMIT, and UNION operations.
 *
 * @package JardisPsr\Contract\Database
 */
interface DbQueryBuilderInterface extends
    DbWhereConditionInterface,
    DbJoinInterface,
    DbQueryExistsInterface,
    DbOrderLimitInterface,
    DbSqlGeneratorInterface
{
    /**
     * Adds a Common Table Expression (CTE) to the query using WITH clause.
     *
     * CTEs allow you to define temporary named result sets that can be referenced
     * within the main query. Multiple CTEs can be added by calling this method
     * multiple times. CTEs are executed before the main query and improve readability
     * of complex queries.
     *
     * Examples:
     * ```php
     * $activeUsers = $builder->select('id, name')->from('users')->where('active')->equals(1);
     * $query->with('active_users', $activeUsers)
     *       ->select('*')
     *       ->from('active_users');
     * ```
     *
     * Multiple CTEs:
     * ```php
     * $query->with('cte1', $subquery1)
     *       ->with('cte2', $subquery2)
     *       ->select('*')
     *       ->from('cte1')
     *       ->innerJoin('cte2', 'cte1.id = cte2.id');
     * ```
     *
     * @param string $name The name of the CTE (used to reference it in the main query)
     * @param DbQueryBuilderInterface $query The subquery that defines the CTE
     * @return self Returns the query builder instance for method chaining
     */
    public function with(string $name, DbQueryBuilderInterface $query): self;

    /**
     * Adds a recursive Common Table Expression (CTE) to the query using WITH RECURSIVE.
     *
     * Recursive CTEs are used for hierarchical or tree-structured data, such as
     * organizational charts, category trees, or graph traversal. The subquery
     * typically contains a UNION between an anchor member (base case) and a
     * recursive member that references the CTE itself.
     *
     * Note: Not all databases support recursive CTEs (MySQL 8.0+, PostgresSQL, SQLite 3.8.3+).
     *
     * Example (organizational hierarchy):
     * ```php
     * $recursive = $builder
     *     ->select('id, name, manager_id, 1 as level')
     *     ->from('employees')
     *     ->where('manager_id')->isNull()
     *     ->unionAll(
     *         $builder->select('e.id, e.name, e.manager_id, r.level + 1')
     *             ->from('employees', 'e')
     *             ->innerJoin('org_tree', 'e.manager_id = r.id', 'r')
     *     );
     *
     * $query->withRecursive('org_tree', $recursive)
     *       ->select('*')
     *       ->from('org_tree')
     *       ->orderBy('level');
     * ```
     *
     * @param string $name The name of the recursive CTE
     * @param DbQueryBuilderInterface $query The recursive subquery (must contain UNION)
     * @return self Returns the query builder instance for method chaining
     */
    public function withRecursive(string $name, DbQueryBuilderInterface $query): self;

    /**
     * Specifies the columns to select in the query.
     *
     * This method defines the SELECT clause of the SQL query. Multiple columns
     * can be specified as a comma-separated string. Supports SQL functions,
     * aliases, and expressions.
     *
     * @param string $fields The column names or expressions to select (e.g., 'id, name, COUNT(*) as total')
     * @return self Returns the query builder instance for method chaining
     */
    public function select(string $fields): self;

    /**
     * Includes a subquery in the SELECT clause.
     *
     * Adds a correlated or non-correlated subquery as a column in the result set.
     * The subquery must return a single value (scalar subquery).
     *
     * @param DbQueryBuilderInterface $query The subquery to include in SELECT
     * @param string $alias Required alias for the subquery column
     * @return self Returns the query builder instance for method chaining
     */
    public function selectSubquery(DbQueryBuilderInterface $query, string $alias): self;

    /**
     * Adds the DISTINCT keyword to the SELECT clause.
     *
     * When enabled, duplicate rows in the result set will be eliminated.
     * This should be called before or after select() method.
     *
     * @param bool $isDistinctQuery Whether to make this a DISTINCT query (default: false)
     * @return self Returns the query builder instance for method chaining
     */
    public function distinct(bool $isDistinctQuery = false): self;

    /**
     * Specifies the table or subquery to query from (FROM clause).
     *
     * Defines the primary data source for the query. Can accept either a table name
     * or a subquery builder instance. An optional alias can be provided to reference
     * the data source in JOINs and WHERE clauses.
     *
     * When using a subquery, an alias is required and will be enforced by the implementation.
     * For table names, the alias is optional.
     *
     * @param string|DbQueryBuilderInterface $container The table name or subquery to query from
     * @param string|null $alias Alias for the data source (required for subqueries, optional for tables)
     * @return self Returns the query builder instance for method chaining
     * @throws InvalidArgumentException If $container is a subquery and $alias is null (enforced in implementation)
     */
    public function from(string|DbQueryBuilderInterface $container, ?string $alias = null): self;

    /**
     * Starts a JSON-specific HAVING condition
     *
     * @param string $field The JSON column name
     * @param string|null $openBracket Optional opening bracket(s)
     * @return DbQueryJsonConditionBuilderInterface For JSON-specific operations
     */
    public function havingJson(string $field, ?string $openBracket = null): DbQueryJsonConditionBuilderInterface;

    /**
     * Adds a RIGHT JOIN clause to the query.
     *
     * Joins another table or subquery using RIGHT JOIN (RIGHT OUTER JOIN), returning all rows
     * from the right table and matching rows from the left table (NULL if no match).
     *
     * When using a subquery, an alias is required and will be enforced by the implementation.
     *
     * Examples:
     * - Table: rightJoin('orders', 'orders.user_id = users.id', 'o')
     * - Subquery: rightJoin($subquery, 'subq.id = users.id', 'subq')
     *
     * @param string|DbQueryBuilderInterface $container The table name or subquery to join
     * @param string $constraint The join condition (e.g., 'orders.user_id = users.id')
     * @param string|null $alias Optional alias for the joined table (required for subqueries)
     * @return self Returns the query builder instance for method chaining
     * @throws InvalidArgumentException If $container is a subquery and $alias is null (enforced in implementation)
     */
    public function rightJoin(
        string|DbQueryBuilderInterface $container,
        string $constraint,
        ?string $alias = null
    ): self;

    /**
     * Adds a FULL OUTER JOIN clause to the query.
     *
     * Joins another table or subquery using FULL OUTER JOIN, returning all rows from both tables,
     * with NULLs where no match exists. Note: Not supported by MySQL, mainly for PostgresSQL.
     *
     * When using a subquery, an alias is required and will be enforced by the implementation.
     *
     * Examples:
     * - Table: fullJoin('orders', 'orders.user_id = users.id', 'o')
     * - Subquery: fullJoin($subquery, 'subq.id = users.id', 'subq')
     *
     * @param string|DbQueryBuilderInterface $container The table name or subquery to join
     * @param string $constraint The join condition (e.g., 'orders.user_id = users.id')
     * @param string|null $alias Optional alias for the joined table (required for subqueries)
     * @return self Returns the query builder instance for method chaining
     * @throws InvalidArgumentException If $container is a subquery and $alias is null (enforced in implementation)
     */
    public function fullJoin(
        string|DbQueryBuilderInterface $container,
        string $constraint,
        ?string $alias = null
    ): self;

    /**
     * Adds a CROSS JOIN clause to the query.
     *
     * Performs a Cartesian product, combining every row from the first table
     * with every row from the second table. No join condition is required.
     *
     * When using a subquery, an alias is required and will be enforced by the implementation.
     *
     * Examples:
     * - Table: crossJoin('orders', 'o')
     * - Subquery: crossJoin($subquery, 'subq')
     *
     * @param string|DbQueryBuilderInterface $container The table name or subquery to cross join
     * @param string|null $alias Optional alias for the joined table (required for subqueries)
     * @return self Returns the query builder instance for method chaining
     * @throws InvalidArgumentException If $container is a subquery and $alias is null (enforced in implementation)
     */
    public function crossJoin(string|DbQueryBuilderInterface $container, ?string $alias = null): self;

    /**
     * Combines this query with another using UNION.
     *
     * Merges the results of two queries, removing duplicate rows.
     * Both queries must have the same number and compatible types of columns.
     *
     * @param DbQueryBuilderInterface $query The query to union with this query
     * @return self Returns the query builder instance for method chaining
     */
    public function union(DbQueryBuilderInterface $query): self;

    /**
     * Combines this query with another using UNION ALL.
     *
     * Merges the results of two queries, keeping all rows including duplicates.
     * Both queries must have the same number and compatible types of columns.
     *
     * @param DbQueryBuilderInterface $query The query to union with this query
     * @return self Returns the query builder instance for method chaining
     */
    public function unionAll(DbQueryBuilderInterface $query): self;

    /**
     * Specifies columns for the GROUP BY clause.
     *
     * Groups result rows by one or more columns. Typically used with aggregate
     * functions like COUNT, SUM, AVG. Multiple columns can be passed as variadic arguments.
     *
     * @param string ...$fields One or more column names to group by
     * @return self Returns the query builder instance for method chaining
     */
    public function groupBy(string ...$fields): self;

    /**
     * Adds a HAVING clause for filtering grouped results.
     *
     * Filters groups created by GROUP BY based on aggregate conditions.
     * Similar to WHERE but operates on grouped data. Returns a condition builder
     * for specifying the comparison.
     *
     * @param string $expression The aggregate expression or column to filter (e.g., 'COUNT(*)')
     * @param string|null $openBracket Optional opening bracket(s) for grouping conditions
     * @return DbQueryConditionBuilderInterface Returns the condition builder for defining comparisons
     */
    public function having(string $expression, ?string $openBracket = null): DbQueryConditionBuilderInterface;

    /**
     * Adds a window function to the SELECT clause.
     *
     * Window functions perform calculations across a set of rows related to the
     * current row, using the OVER clause. This method starts building a window
     * function specification and returns a window builder for defining PARTITION BY,
     * ORDER BY, and frame specifications.
     *
     * Supported in MySQL 8.0+, MariaDB 10.2+, PostgreSQL 8.4+, SQLite 3.25.0+.
     *
     * Common window functions:
     * - Ranking: ROW_NUMBER, RANK, DENSE_RANK, NTILE
     * - Value: LAG, LEAD, FIRST_VALUE, LAST_VALUE, NTH_VALUE
     * - Aggregate: SUM, AVG, COUNT, MIN, MAX (with OVER clause)
     *
     * Examples:
     * ```php
     * // ROW_NUMBER() OVER (PARTITION BY department ORDER BY salary DESC) as row_num
     * $query->select('id, name, salary')
     *       ->selectWindow('ROW_NUMBER', 'row_num')
     *           ->partitionBy('department')
     *           ->windowOrderBy('salary', 'DESC')
     *           ->endWindow()
     *       ->from('employees');
     *
     * // Running total: SUM(amount) OVER (ORDER BY date) as running_total
     * $query->select('date, amount')
     *       ->selectWindow('SUM', 'running_total', 'amount')
     *           ->windowOrderBy('date')
     *           ->endWindow()
     *       ->from('transactions');
     *
     * // Rank with ties: RANK() OVER (ORDER BY score DESC) as rank
     * $query->selectWindow('RANK', 'rank')
     *       ->windowOrderBy('score', 'DESC')
     *       ->endWindow()
     *       ->from('players');
     *
     * // LAG to compare with previous row
     * $query->select('date, price')
     *       ->selectWindow('LAG', 'prev_price', 'price, 1')
     *           ->windowOrderBy('date')
     *           ->endWindow()
     *       ->from('stock_prices');
     * ```
     *
     * @param string $function Window function name (e.g., 'ROW_NUMBER', 'RANK', 'SUM', 'AVG')
     * @param string $alias Column alias for the window function result
     * @param string|null $args function arguments (e.g.,'amount' for SUM(amount), 'price, 1' for LAG(price, 1))
     * @return DbWindowBuilderInterface Returns the window builder for defining window specification
     */
    public function selectWindow(string $function, string $alias, ?string $args = null): DbWindowBuilderInterface;

    /**
     * Defines a named window specification that can be referenced by multiple window functions.
     *
     * Named windows allow you to define a window specification once and reuse it
     * across multiple window functions, improving query readability and maintainability.
     * The window is defined using the WINDOW clause and can be referenced by name
     * in window functions.
     *
     * This is particularly useful when multiple window functions share the same
     * PARTITION BY and ORDER BY clauses.
     *
     * Note: Named windows are supported in all databases that support window functions
     * (MySQL 8.0+, MariaDB 10.2+, PostgreSQL 8.4+, SQLite 3.25.0+).
     *
     * Examples:
     * ```php
     * // Define a named window and use it in multiple functions
     * $query->select('id, name, salary')
     *       ->window('dept_window')
     *           ->partitionBy('department')
     *           ->windowOrderBy('salary', 'DESC')
     *           ->endWindow()
     *       ->selectWindowRef('ROW_NUMBER', 'dept_window', 'row_num')
     *       ->selectWindowRef('RANK', 'dept_window', 'rank')
     *       ->from('employees');
     * // SQL: SELECT id, name, salary,
     * //             ROW_NUMBER() OVER dept_window as row_num,
     * //             RANK() OVER dept_window as rank
     * //      FROM employees
     * //      WINDOW dept_window AS (PARTITION BY department ORDER BY salary DESC)
     *
     * // Multiple named windows
     * $query->window('by_dept')
     *           ->partitionBy('department')
     *           ->windowOrderBy('hire_date')
     *           ->endWindow()
     *       ->window('by_team')
     *           ->partitionBy('team')
     *           ->windowOrderBy('performance', 'DESC')
     *           ->endWindow()
     *       ->selectWindowRef('ROW_NUMBER', 'by_dept', 'dept_seniority')
     *       ->selectWindowRef('RANK', 'by_team', 'team_rank')
     *       ->from('employees');
     * ```
     *
     * @param string $name The name of the window (used to reference it in selectWindowRef)
     * @return DbWindowBuilderInterface Returns the window builder for defining window specification
     */
    public function window(string $name): DbWindowBuilderInterface;

    /**
     * Adds a window function that references a named window.
     *
     * Uses a previously defined named window (created with the window() method)
     * in a window function. This allows reusing the same window specification
     * across multiple functions without repeating the PARTITION BY and ORDER BY clauses.
     *
     * Examples:
     * ```php
     * // First define the named window
     * $query->window('dept_salary_window')
     *           ->partitionBy('department')
     *           ->windowOrderBy('salary', 'DESC')
     *           ->endWindow();
     *
     * // Then use it in multiple window functions
     * $query->selectWindowRef('ROW_NUMBER', 'dept_salary_window', 'row_num')
     *       ->selectWindowRef('RANK', 'dept_salary_window', 'salary_rank')
     *       ->selectWindowRef('DENSE_RANK', 'dept_salary_window', 'dense_rank')
     *       ->select('id, name, department, salary')
     *       ->from('employees');
     *
     * // With function arguments (e.g., SUM, LAG)
     * $query->window('time_window')
     *           ->windowOrderBy('timestamp')
     *           ->endWindow()
     *       ->selectWindowRef('SUM', 'time_window', 'running_total', 'amount')
     *       ->selectWindowRef('LAG', 'time_window', 'prev_value', 'value, 1')
     *       ->from('metrics');
     * ```
     *
     * @param string $function Window function name (e.g., 'ROW_NUMBER', 'RANK', 'SUM')
     * @param string $windowName The name of the window defined with window() method
     * @param string $alias Column alias for the window function result
     * @param string|null $args Optional function arguments (e.g., 'amount' for SUM(amount))
     * @return self Returns the query builder instance for method chaining
     */
    public function selectWindowRef(string $function, string $windowName, string $alias, ?string $args = null): self;
}
