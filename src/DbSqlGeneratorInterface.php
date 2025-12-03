<?php

declare(strict_types=1);

namespace JardisPsr\DbQuery;

use InvalidArgumentException;

/**
 * Interface for SQL query generation in query builders.
 *
 * This interface provides the method for generating the final SQL string
 * or prepared query object from a query builder. It implements the Interface
 * Segregation Principle by isolating SQL generation functionality that is
 * shared across SELECT, UPDATE, and DELETE query builders.
 *
 * The interface supports multiple SQL dialects (MySQL, PostgresSQL, SQLite)
 * and can generate both raw SQL strings and prepared statements with
 * parameter bindings.
 *
 * @package JardisPsr\Contract\Database
 */
interface DbSqlGeneratorInterface
{
    /**
     * Generates an SQL query based on the specified dialect and preparation mode.
     *
     * Creates an SQL query string or a prepared query object, depending on the
     * options provided. This is the final method called in the fluent interface
     * chain to produce executable SQL.
     *
     * When $prepared is true (default), returns a DbPreparedQueryInterface object
     * containing the SQL template with placeholders and a separate array of bound
     * parameters. This is the recommended approach for security (SQL injection
     * prevention) and performance.
     *
     * When $prepared is false, returns a raw SQL string with values directly
     * interpolated. This should only be used for debugging or logging purposes.
     *
     * Examples:
     * ```php
     * // SELECT: Get prepared query (recommended)
     * $prepared = $query->select('*')
     *                   ->from('users')
     *                   ->where('status')->equals('active')
     *                   ->sql('mysql', true);
     * // Result: SELECT * FROM users WHERE status = ?
     * // Params: ['active']
     *
     * // SELECT: Get raw SQL (debugging only)
     * $rawSql = $query->select('*')
     *                 ->from('users')
     *                 ->where('id')->equals(123)
     *                 ->sql('mysql', false);
     * // Result: SELECT * FROM users WHERE id = 123
     *
     * // UPDATE: Generate for PostgresSQL
     * $prepared = $update->table('users')
     *                    ->set('last_login', 'NOW()')
     *                    ->where('id')->equals(456)
     *                    ->sql('pgsql', true);
     *
     * // DELETE: Generate for SQLite
     * $prepared = $delete->from('logs')
     *                    ->where('created_at')->lower(raw('date("now", "-1 year")'))
     *                    ->sql('sqlite', true);
     * ```
     *
     * @param string $dialect The SQL dialect to use (e.g., 'mysql', 'pgsql', 'sqlite')
     * @param bool $prepared Set to true to return a prepared query (default), false for raw SQL string
     * @param string|null $version Optional database version for dialect-specific syntax (e.g., '8.0', '5.7')
     * @return string|DbPreparedQueryInterface Returns a DbPreparedQueryInterface with SQL and parameters
     *                                        when $prepared is true, or a raw SQL string when $prepared is false
     * @throws InvalidArgumentException If an unsupported dialect is provided (enforced in implementation)
     */
    public function sql(
        string $dialect,
        bool $prepared = true,
        ?string $version = null
    ): string|DbPreparedQueryInterface;
}
