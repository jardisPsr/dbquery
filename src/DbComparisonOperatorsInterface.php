<?php

declare(strict_types=1);

namespace JardisPsr\DbQuery;

/**
 * Interface for common comparison operators in query conditions.
 *
 * This interface defines standard comparison operations that work across
 * regular column conditions and JSON column conditions in SELECT, UPDATE,
 * and DELETE queries. It implements the Interface Segregation Principle
 * by extracting commonly used comparison logic.
 *
 * All methods return a union type of possible query builders to support
 * the fluent interface pattern across different query types.
 *
 * @package JardisPsr\Contract\Database
 */
interface DbComparisonOperatorsInterface
{
    /**
     * Adds a condition to check if a value equals the specified parameter.
     *
     * Example:
     * ```php
     * // Compare with literal value (parameter binding)
     * $query->where('status')->equals('active');
     * $query->where('age')->equals(25);
     *
     * // Compare with column or expression
     * $query->where('start_date')->equals('end_date', null, true);
     * ```
     *
     * @param string|int|float|bool|null|DbQueryBuilderInterface|ExpressionInterface $value
     *        The value to compare. Can be:
     *        - Scalar value (string, int, float, bool)
     *        - null
     *        - DbQueryBuilderInterface (subquery)
     *        - ExpressionInterface (raw SQL expression)
     * @param string|null $closeBracket Optional closing bracket(s) to append to the condition (e.g., ')' or '))')
     * @return DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface The builder for method chaining
     */
    public function equals(
        mixed $value,
        ?string $closeBracket = null
    ): DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface;

    /**
     * Adds a condition to check if a value does not equal the specified parameter.
     *
     * Example:
     * ```php
     * // Compare with literal value
     * $query->where('status')->notEquals('deleted');
     *
     * // Compare with column
     * $query->where('created_by')->notEquals('modified_by', null, true);
     * ```
     *
     * @param string|int|float|bool|null|DbQueryBuilderInterface|ExpressionInterface $value
     *        The value to compare. Can be:
     *        - Scalar value (string, int, float, bool)
     *        - null
     *        - DbQueryBuilderInterface (subquery)
     *        - ExpressionInterface (raw SQL expression)
     * @param string|null $closeBracket Optional closing bracket(s) to append to the condition
     * @return DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface The builder for chaining
     */
    public function notEquals(
        mixed $value,
        ?string $closeBracket = null
    ): DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface;

    /**
     * Adds a condition to check if a value is greater than the specified parameter.
     *
     * Example:
     * ```php
     * // Compare with literal value
     * $query->where('age')->greater(18);
     * $query->where('price')->greater(99.99);
     *
     * // Compare with column
     * $query->where('activeFrom')->greater('activeUntil', null, true);
     * ```
     *
     * @param string|int|float|bool|null|DbQueryBuilderInterface|ExpressionInterface $value
     *        The value to compare. Can be:
     *        - Scalar value (string, int, float, bool)
     *        - null
     *        - DbQueryBuilderInterface (subquery)
     *        - ExpressionInterface (raw SQL expression)
     * @param string|null $closeBracket Optional closing bracket(s) to append to the condition
     * @return DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface The builder for chaining
     */
    public function greater(
        mixed $value,
        ?string $closeBracket = null
    ): DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface;

    /**
     * Adds a condition to check if a value is greater than or equal to the specified parameter.
     *
     * Example:
     * ```php
     * // Compare with literal value
     * $query->where('age')->greaterEquals(18);
     *
     * // Compare with column
     * $query->where('salary')->greaterEquals('min_salary', null, true);
     * ```
     *
     * @param string|int|float|bool|null|DbQueryBuilderInterface|ExpressionInterface $value
     *        The value to compare. Can be:
     *        - Scalar value (string, int, float, bool)
     *        - null
     *        - DbQueryBuilderInterface (subquery)
     *        - ExpressionInterface (raw SQL expression)
     * @param string|null $closeBracket Optional closing bracket(s) to append to the condition
     * @return DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface The builder for chaining
     */
    public function greaterEquals(
        mixed $value,
        ?string $closeBracket = null
    ): DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface;

    /**
     * Adds a condition to check if a value is less than the specified parameter.
     *
     * Example:
     * ```php
     * // Compare with literal value
     * $query->where('age')->lower(65);
     * $query->where('price')->lower(100.00);
     *
     * // Compare with column
     * $query->where('stock')->lower('min_stock', null, true);
     * ```
     *
     * @param string|int|float|bool|null|DbQueryBuilderInterface|ExpressionInterface $value
     *        The value to compare. Can be:
     *        - Scalar value (string, int, float, bool)
     *        - null
     *        - DbQueryBuilderInterface (subquery)
     *        - ExpressionInterface (raw SQL expression)
     * @param string|null $closeBracket Optional closing bracket(s) to append to the condition
     * @return DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface The builder for method chaining
     */
    public function lower(
        mixed $value,
        ?string $closeBracket = null
    ): DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface;

    /**
     * Adds a condition to check if a value is less than or equal to the specified parameter.
     *
     * Example:
     * ```php
     * // Compare with literal value
     * $query->where('age')->lowerEquals(65);
     *
     * // Compare with column
     * $query->where('discount')->lowerEquals('max_discount', null, true);
     * ```
     *
     * @param string|int|float|bool|null|DbQueryBuilderInterface|ExpressionInterface $value
     *        The value to compare. Can be:
     *        - Scalar value (string, int, float, bool)
     *        - null
     *        - DbQueryBuilderInterface (subquery)
     *        - ExpressionInterface (raw SQL expression)
     * @param string|null $closeBracket Optional closing bracket(s) to append to the condition
     * @return DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface The builder for method chaining
     */
    public function lowerEquals(
        mixed $value,
        ?string $closeBracket = null
    ): DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface;

    /**
     * Adds a condition to check if a value is in a list of values or a subquery result.
     *
     * Accepts either an array of values or a subquery builder for dynamic value lists.
     * When using a subquery, the subquery must return a single column.
     *
     * Examples:
     * ```php
     * // With array
     * $query->where('status')->in(['active', 'pending', 'approved']);
     *
     * // With subquery
     * $activeUserIds = $builder->select('id')->from('users')->where('active')->equals(1);
     * $query->where('user_id')->in($activeUserIds);
     * ```
     *
     * @param array<mixed>|DbQueryBuilderInterface $value Array of values or subquery returning values
     * @param string|null $closeBracket Optional closing bracket(s) to append to the condition
     * @return DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface The builder for method chaining
     */
    public function in(
        array|DbQueryBuilderInterface $value,
        ?string $closeBracket = null
    ): DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface;

    /**
     * Adds a condition to check if a value is NOT in a list of values or a subquery result.
     *
     * Accepts either an array of values or a subquery builder for dynamic exclusion lists.
     * When using a subquery, the subquery must return a single column.
     *
     * Examples:
     * ```php
     * // With array
     * $query->where('status')->notIn(['deleted', 'banned']);
     *
     * // With subquery
     * $inactiveUserIds = $builder->select('id')->from('users')->where('active')->equals(0);
     * $query->where('user_id')->notIn($inactiveUserIds);
     * ```
     *
     * @param array<mixed>|DbQueryBuilderInterface $value Array of values or subquery returning values to exclude
     * @param string|null $closeBracket Optional closing bracket(s) to append to the condition
     * @return DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface The builder for method chaining
     */
    public function notIn(
        array|DbQueryBuilderInterface $value,
        ?string $closeBracket = null
    ): DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface;

    /**
     * Adds a LIKE condition for pattern matching.
     *
     * Supports SQL wildcards: % (any characters) and _ (single character).
     *
     * Examples:
     * ```php
     * $query->where('name')->like('John%');      // Starts with 'John'
     * $query->where('email')->like('%@gmail.com'); // Ends with '@gmail.com'
     * $query->where('code')->like('A_C');        // A, any char, C
     * ```
     *
     * @param string|int|float|bool|null|DbQueryBuilderInterface|ExpressionInterface $value
     *        The value to compare. Can be:
     *        - Scalar value (string, int, float, bool)
     *        - null
     *        - DbQueryBuilderInterface (subquery)
     *        - ExpressionInterface (raw SQL expression)
     * @param string|null $closeBracket Optional closing bracket(s) to append to the condition
     * @return DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface The builder for method chaining
     */
    public function like(
        mixed $value,
        ?string $closeBracket = null
    ): DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface;

    /**
     * Adds a NOT LIKE condition for negative pattern matching.
     *
     * Example:
     * ```php
     * $query->where('email')->notLike('%@spam.com');
     * ```
     *
     * @param string|int|float|bool|null|DbQueryBuilderInterface|ExpressionInterface $value
     *        The value to compare. Can be:
     *        - Scalar value (string, int, float, bool)
     *        - null
     *        - DbQueryBuilderInterface (subquery)
     *        - ExpressionInterface (raw SQL expression)
     * @param string|null $closeBracket Optional closing bracket(s) to append to the condition
     * @return DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface The builder for method chaining
     */
    public function notLike(
        mixed $value,
        ?string $closeBracket = null
    ): DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface;

    /**
     * Adds an IS NULL condition.
     *
     * Example:
     * ```php
     * $query->where('deleted_at')->isNull();
     * $query->where('parent_id')->isNull();
     * ```
     *
     * @param string|null $closeBracket Optional closing bracket(s) to append to the condition
     * @return DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface The builder for method chaining
     */
    public function isNull(
        ?string $closeBracket = null
    ): DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface;

    /**
     * Adds an IS NOT NULL condition.
     *
     * Example:
     * ```php
     * $query->where('email')->isNotNull();
     * $query->where('verified_at')->isNotNull();
     * ```
     *
     * @param string|null $closeBracket Optional closing bracket(s) to append to the condition
     * @return DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface The builder for method chaining
     */
    public function isNotNull(
        ?string $closeBracket = null
    ): DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface;
}
