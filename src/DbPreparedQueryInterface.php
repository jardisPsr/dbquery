<?php

declare(strict_types=1);

namespace JardisPsr\DbQuery;

/**
 * Represents a prepared SQL query with parameter bindings.
 *
 * This immutable value object contains the SQL query string with placeholders
 * and the corresponding parameter bindings for safe execution with prepared statements.
 *
 * Use this with PDO or database connection implementations to execute queries safely.
 *
 * @package JardisPsr\Contract\Database
 */
interface DbPreparedQueryInterface
{
    /**
     * Returns the SQL query string with placeholders.
     *
     * The query contains placeholders (? or :name) for parameter binding.
     *
     * @return string The SQL query with placeholders
     */
    public function sql(): string;

    /**
     * Returns the parameter bindings for the prepared statement.
     *
     * The array structure depends on the placeholder format:
     * - Positional (?,?): Numeric array [0 => 'value1', 1 => 'value2']
     * - Named (:name): Associative array ['name' => 'value']
     *
     * @return array<int|string, mixed> Parameter bindings (numeric for positional, associative for named)
     */
    public function bindings(): array;

    /**
     * Returns the query type (SELECT, INSERT, UPDATE, DELETE, etc.).
     *
     * Useful for logging, debugging, or query analysis.
     *
     * @return string The query type in uppercase (e.g., 'SELECT')
     */
    public function type(): string;
}
