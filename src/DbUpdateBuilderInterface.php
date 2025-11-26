<?php

declare(strict_types=1);

namespace JardisPsr\DbQuery;

/**
 * Interface for building SQL UPDATE commands using a fluent interface pattern.
 *
 * This interface provides methods to construct UPDATE queries programmatically,
 * supporting WHERE conditions, JOINs (for some dialects), LIMIT, and ORDER BY.
 *
 * @package JardisPsr\Contract\Database
 */
interface DbUpdateBuilderInterface extends
    DbWhereConditionInterface,
    DbQueryExistsInterface,
    DbJoinInterface,
    DbOrderLimitInterface,
    DbSqlGeneratorInterface
{
    /**
     * Specifies the table to update.
     *
     * This method defines the target table for the UPDATE operation.
     * An optional alias can be provided for use in JOINs or complex conditions.
     *
     * Example:
     * ```php
     * $command->update()->table('users', 'u');
     * ```
     *
     * @param string $container The table name
     * @param string|null $alias Optional alias for the table
     * @return self Returns the command builder instance for method chaining
     */
    public function table(string $container, ?string $alias = null): self;

    /**
     * Sets a single column to a specific value.
     *
     * Can be called multiple times to set multiple columns.
     * Values will be properly escaped and bound as parameters.
     *
     * Supported value types:
     * - Scalar values (string, int, float, bool, null): Automatically bound as prepared statement parameters
     * - DbQueryBuilderInterface: Treated as subquery and wrapped in parentheses
     *
     * Examples:
     * ```php
     * // Simple scalar values (bound as parameters)
     * $command->update()
     *     ->table('users')
     *     ->set('status', 'active')
     *     ->set('age', 25)
     *     ->set('verified', true)
     *     ->where('id')->equals(123);
     *
     * // Subquery as value
     * $maxSalary = $builder->select('MAX(salary)')
     *     ->from('employees')
     *     ->where('department')->equals('IT');
     *
     * $command->update()
     *     ->table('employees')
     *     ->set('salary', $maxSalary)
     *     ->where('department')->equals('IT');
     * ```
     * Values can be: scalar, null, DbQueryBuilderInterface (subquery), or ExpressionInterface
     *
     * @param string $field The column name to update
     * @param string|int|float|bool|null|DbQueryBuilderInterface $value The value to set
     * @return self Returns the command builder instance for method chaining
     */
    public function set(
        string $field,
        string|int|float|bool|null|DbQueryBuilderInterface|ExpressionInterface $value
    ): self;

    /**
     * Sets multiple columns using an associative array.
     *
     * Provides a convenient way to set multiple columns at once.
     * Can be called multiple times; later calls will add/override values.
     *
     * Each value in the array follows the same rules as the set() method:
     * - Scalar values: Automatically bound as prepared statement parameters
     * - DbQueryBuilderInterface: Treated as subquery and wrapped in parentheses
     *
     * Examples:
     * ```php
     * // Scalar values
     * $command->update()
     *     ->table('users')
     *     ->setMultiple([
     *         'status' => 'active',
     *         'age' => 30,
     *         'verified' => true
     *     ])
     *     ->where('id')->equals(123);
     *
     * // With subquery
     * $avgSalary = $builder->select('AVG(salary)')->from('employees');
     *
     * $command->update()
     *     ->table('employees')
     *     ->setMultiple([
     *         'salary' => $avgSalary,
     *         'bonus' => 1000
     *     ])
     *     ->where('active')->equals(1);
     * ```
     * Values can be: scalar, null, DbQueryBuilderInterface (subquery), or ExpressionInterface
     *
     * @param array<string, string|int|float|bool|null|DbQueryBuilderInterface|ExpressionInterface> $data
     * @return self Returns the command builder instance for method chaining
     */
    public function setMultiple(array $data): self;

    /**
     * Silently ignores errors during UPDATE operation.
     *
     * When errors occur (e.g., duplicate key, constraint violations), they are suppressed
     * and the UPDATE continues without failing. Rows that would cause errors are skipped.
     *
     * Database-specific behavior:
     * - MySQL/MariaDB: UPDATE IGNORE
     * - PostgreSQL: Not directly supported (requires error handling in application)
     * - SQLite: UPDATE OR IGNORE
     *
     * Examples:
     * ```php
     * // Update with constraint violation protection
     * $command->update()
     *     ->table('users')
     *     ->set('email', 'new@example.com')
     *     ->where('status')->equals('active')
     *     ->ignore();
     *
     * // Bulk update - skip rows with errors
     * $command->update()
     *     ->table('products')
     *     ->set('category_id', 5)
     *     ->where('old_category_id')->equals(3)
     *     ->ignore();
     * ```
     *
     * Use cases:
     * - Updating email addresses that might cause unique constraint violations
     * - Bulk updates where some rows might violate constraints
     * - Data migrations where partial success is acceptable
     *
     * Warning: Use with caution as it silently suppresses errors
     *
     * @return self Returns the command builder instance for method chaining
     */
    public function ignore(): self;
}
