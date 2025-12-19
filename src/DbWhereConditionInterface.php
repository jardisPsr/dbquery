<?php

declare(strict_types=1);

namespace JardisPsr\DbQuery;

/**
 * Interface for WHERE condition methods in SQL query builders.
 *
 * This interface provides methods to add WHERE, AND, and OR conditions
 * to SQL queries, including support for both standard and JSON-based conditions.
 * It implements the Interface Segregation Principle by isolating condition-related
 * functionality that is shared across SELECT, UPDATE, and DELETE query builders.
 *
 * @package JardisPsr\Contract\Database
 */
interface DbWhereConditionInterface
{
    /**
     * Starts a WHERE condition clause.
     *
     * Initiates a condition chain by specifying the column to compare.
     * If no column is provided, it can be used to add EXISTS or other
     * standalone conditions. Returns a condition builder for specifying
     * the comparison operator and value.
     *
     * Example:
     * ```php
     * // Simple column comparison
     * $query->where('status')->equals('active');
     * $query->where('age')->greaterThan(18);
     * $query->where('name', '(')->like('%John%')->or('name')->like('%Jane%', ')');
     *
     * // Expression for functions or calculations
     * $query->where(new Expression('LOWER(name)'))->equals('john');
     * $query->where(new Expression('DATE(created_at)'))->equals('2024-01-01');
     * $query->where(new Expression('price * quantity'))->greater(1000);
     * ```
     *
     * @param string|ExpressionInterface|null $field The column name or expression to compare (optional for EXISTS)
     * @param string|null $openBracket Optional opening bracket(s) for grouping conditions (e.g., '(' or '((')
     * @return DbQueryConditionBuilderInterface Returns the condition builder for defining comparisons
     */
    public function where(
        string|ExpressionInterface|null $field = null,
        ?string $openBracket = null
    ): DbQueryConditionBuilderInterface;

    /**
     * Adds an AND condition to the WHERE clause.
     *
     * Chains an additional condition with AND logic. Typically used to refine
     * queries by combining multiple conditions. If no column is provided,
     * can be used for standalone conditions like EXISTS.
     *
     * Examples:
     * ```php
     * // Simple column comparison
     * $query->where('status')->equals('active')
     *       ->and('age')->greater(18);
     *
     * // Expression for functions or calculations
     * $query->where('status')->equals('active')
     *       ->and(new Expression('YEAR(created_at)'))->equals(2024);
     * ```
     *
     * @param string|ExpressionInterface|null $field The column name or expression to compare (optional for EXISTS)
     * @param string|null $openBracket Optional opening bracket(s) for grouping conditions
     * @return DbQueryConditionBuilderInterface Returns the condition builder for defining comparisons
     */
    public function and(
        string|ExpressionInterface|null $field = null,
        ?string $openBracket = null
    ): DbQueryConditionBuilderInterface;

    /**
     * Adds an "OR" condition to the query being built.
     *
     * Examples:
     * ```php
     * // Simple column comparison
     * $query->where('status')->equals('active')
     *       ->or('status')->equals('pending');
     *
     * // Expression for functions or calculations
     * $query->where('name')->like('%John%')
     *       ->or(new Expression('LOWER(email)'))->like('%john%');
     * ```
     *
     * @param string|ExpressionInterface|null $field The column name or expression to compare (optional for EXISTS)
     * @param string|null $openBracket An optional opening bracket for grouping conditions. Pass null if not needed.
     * @return DbQueryConditionBuilderInterface Returns the current instance to allow method chaining.
     */
    public function or(
        string|ExpressionInterface|null $field = null,
        ?string $openBracket = null
    ): DbQueryConditionBuilderInterface;

    /**
     * Starts a JSON-specific WHERE condition.
     *
     * Initiates a condition chain for JSON column data, enabling JSON path
     * expressions and JSON-specific comparison operators.
     *
     * Example:
     * ```php
     * $query->whereJson('metadata')->path('$.user.name')->equals('John');
     * $query->whereJson('settings')->contains(['theme' => 'dark']);
     * ```
     *
     * @param string $field The JSON column name
     * @param string|null $openBracket Optional opening bracket(s) for grouping conditions
     * @return DbQueryJsonConditionBuilderInterface Returns the JSON condition builder for JSON-specific operations
     */
    public function whereJson(string $field, ?string $openBracket = null): DbQueryJsonConditionBuilderInterface;

    /**
     * Starts a JSON-specific AND condition.
     *
     * Chains an additional JSON condition with AND logic.
     *
     * Example:
     * ```php
     * $query->whereJson('metadata')->path('$.user.role')->equals('admin')
     *       ->andJson('metadata')->path('$.user.active')->equals(true);
     * ```
     *
     * @param string $field The JSON column name
     * @param string|null $openBracket Optional opening bracket(s) for grouping conditions
     * @return DbQueryJsonConditionBuilderInterface Returns the JSON condition builder for JSON-specific operations
     */
    public function andJson(string $field, ?string $openBracket = null): DbQueryJsonConditionBuilderInterface;

    /**
     * Starts a JSON-specific OR condition.
     *
     * Chains an alternative JSON condition with OR logic.
     *
     * Example:
     * ```php
     * $query->whereJson('metadata')->path('$.status')->equals('active')
     *       ->orJson('metadata')->path('$.status')->equals('pending');
     * ```
     *
     * @param string $field The JSON column name
     * @param string|null $openBracket Optional opening bracket(s) for grouping conditions
     * @return DbQueryJsonConditionBuilderInterface Returns the JSON condition builder for JSON-specific operations
     */
    public function orJson(string $field, ?string $openBracket = null): DbQueryJsonConditionBuilderInterface;
}
