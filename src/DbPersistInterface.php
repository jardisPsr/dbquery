<?php

declare(strict_types=1);

namespace JardisPsr\DbQuery;

use InvalidArgumentException;

/**
 * Interface for simple CRUD operations based on primary keys.
 *
 * Provides a simplified interface for common database operations
 * when working with single records identified by their primary key.
 */
interface DbPersistInterface
{
    /**
     * Creates an INSERT query with automatic primary key handling
     *
     * @param string $table The table name
     * @param array<string, mixed> $data Associative array of column => value pairs
     * @param string $primaryKey The name of the primary key column
     * @param bool $autoIncrement Whether the primary key is auto-increment (removes it from INSERT)
     * @param string $dialect The SQL dialect (mysql, postgres, sqlite)
     * @param string|null $version Database version (e.g., '8.0'). Uses default if null.
     * @return DbPreparedQueryInterface The prepared query with SQL and bindings
     * @throws InvalidArgumentException If data is empty or invalid
     */
    public function insert(
        string $table,
        array $data,
        string $primaryKey,
        bool $autoIncrement = true,
        string $dialect = 'mysql',
        ?string $version = null
    ): DbPreparedQueryInterface;

    /**
     * Creates an UPDATE query with WHERE condition on primary key
     *
     * @param string $table The table name
     * @param array<string, mixed> $data Associative array of column => value pairs to update
     * @param string $primaryKey The name of the primary key column
     * @param mixed $primaryValue The primary key value for the WHERE condition
     * @param string $dialect The SQL dialect (mysql, postgres, sqlite)
     * @param string|null $version Database version (e.g., '8.0'). Uses default if null.
     * @return DbPreparedQueryInterface The prepared query with SQL and bindings
     * @throws InvalidArgumentException If data is empty, primary value is invalid, or primary key is in data
     */
    public function update(
        string $table,
        array $data,
        string $primaryKey,
        mixed $primaryValue,
        string $dialect = 'mysql',
        ?string $version = null
    ): DbPreparedQueryInterface;

    /**
     * Creates a DELETE query with WHERE condition on primary key
     *
     * @param string $table The table name
     * @param string $primaryKey The name of the primary key column
     * @param mixed $primaryValue The primary key value for the WHERE condition
     * @param string $dialect The SQL dialect (mysql, postgres, sqlite)
     * @param string|null $version Database version (e.g., '8.0'). Uses default if null.
     * @return DbPreparedQueryInterface The prepared query with SQL and bindings
     * @throws InvalidArgumentException If primary value is invalid
     */
    public function delete(
        string $table,
        string $primaryKey,
        mixed $primaryValue,
        string $dialect = 'mysql',
        ?string $version = null
    ): DbPreparedQueryInterface;
}
