<?php

declare(strict_types=1);

namespace JardisPsr\DbQuery;

/**
 * Interface for building conditions in SQL queries and commands.
 *
 * This interface provides methods for constructing WHERE clause conditions
 * with various comparison operators. It can be used for both SELECT queries
 * and command queries (UPDATE, DELETE).
 *
 * Most comparison operators (equals, greater, in, like, etc.) are inherited
 * from DbComparisonOperatorsInterface. This interface adds range-specific
 * operators (between, notBetween) that are unique to standard conditions.
 *
 * @package JardisPsr\Contract\Database
 */
interface DbQueryConditionBuilderInterface extends DbComparisonOperatorsInterface, DbQueryExistsInterface
{
    /**
     * Constructs a condition to filter values between a specified range.
     *
     * Checks if a value falls within the inclusive range [value1, value2].
     *
     * Examples:
     * ```php
     * // Compare with literal values
     * $query->where('age')->between(18, 65);
     * $query->where('price')->between(10.00, 99.99);
     * $query->where('created_at')->between('2024-01-01', '2024-12-31');
     *
     * // Compare with columns using ExpressionInterface
     * $query->where('current_price')->between(
     *     raw('min_price'),
     *     raw('max_price'),
     *     ')'
     * );
     * ```
     *
     * @param string|int|float|bool|null|DbQueryBuilderInterface|ExpressionInterface $min
     *        The minimum value to compare. Can be:
     *        - Scalar value (string, int, float, bool)
     *        - null
     *        - DbQueryBuilderInterface (subquery)
     *        - ExpressionInterface (raw SQL expression)
     * @param string|int|float|bool|null|DbQueryBuilderInterface|ExpressionInterface $max
     *        The value to compare. Can be:
     *        - Scalar value (string, int, float, bool)
     *        - null
     *        - DbQueryBuilderInterface (subquery)
     *        - ExpressionInterface (raw SQL expression)
     * @param string|null $closeBracket An optional closing bracket(s) to append to the condition
     * @return DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface The builder for method chaining
     */
    public function between(
        mixed $min,
        mixed $max,
        ?string $closeBracket = null
    ): DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface;

    /**
     * Constructs a condition where the value is NOT between the provided range.
     *
     * Checks if a value falls outside the inclusive range [value1, value2].
     *
     * Examples:
     * ```php
     * // Compare with literal values
     * $query->where('age')->notBetween(0, 17);  // Exclude minors
     * $query->where('score')->notBetween(40, 60);  // Exclude middle range
     *
     * // Compare with columns using ExpressionInterface
     * $query->where('value')->notBetween(
     *     raw('threshold_min'),
     *     raw('threshold_max'),
     *     ')'
     * );
     * ```
     *
     * @param string|int|float|bool|null|DbQueryBuilderInterface|ExpressionInterface $min
     *        The minimum value to compare. Can be:
     *        - Scalar value (string, int, float, bool)
     *        - null
     *        - DbQueryBuilderInterface (subquery)
     *        - ExpressionInterface (raw SQL expression)
     * @param string|int|float|bool|null|DbQueryBuilderInterface|ExpressionInterface $max
     *        The value to compare. Can be:
     *        - Scalar value (string, int, float, bool)
     *        - null
     *        - DbQueryBuilderInterface (subquery)
     *        - ExpressionInterface (raw SQL expression)
     * @param string|null $closeBracket An optional closing bracket(s) to append to the condition
     * @return DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface The builder for method chaining
     */
    public function notBetween(
        mixed $min,
        mixed $max,
        ?string $closeBracket = null
    ): DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface;
}
