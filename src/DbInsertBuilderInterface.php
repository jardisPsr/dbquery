<?php

declare(strict_types=1);

namespace JardisPsr\DbQuery;

use InvalidArgumentException;

/**
 * Interface for building SQL INSERT commands using a fluent interface pattern.
 *
 * This interface provides methods to construct INSERT queries programmatically,
 * supporting single-row inserts, multi-row inserts, and INSERT...SELECT operations.
 *
 * Note: INSERT commands do NOT support WHERE, JOIN, or HAVING clauses.
 *
 * @package JardisPsr\Contract\Database
 */
interface DbInsertBuilderInterface extends DbSqlGeneratorInterface
{
    /**
     * Specifies the table to insert data into.
     *
     * This method defines the target table for the INSERT operation.
     *
     * Example:
     * ```php
     * $command->insert()->into('users');
     * ```
     *
     * @param string $container The table name
     * @return self Returns the command builder instance for method chaining
     */
    public function into(string $container): self;

    /**
     * Specifies the fields for the INSERT operation.
     *
     * This method defines which fields will receive values. The order of fields
     * must match the order of values in the values() method.
     *
     * Example:
     * ```php
     * $command->insert()->into('users')->fields('name', 'email', 'status');
     * ```
     *
     * @param string ...$fields One or more field names
     * @return self Returns the command builder instance for method chaining
     */
    public function fields(string ...$fields): self;

    /**
     * Adds a single row of values to insert.
     *
     * Can be called multiple times to insert multiple rows in one query.
     * The number and order of values must match the fields defined in fields().
     *
     * Examples:
     * ```php
     * $command->insert()
     *     ->into('users')
     *     ->fields('name', 'email', 'status')
     *     ->values('John Doe', 'john@example.com', 'active')
     *     ->values('Jane Smith', 'jane@example.com', 'active');
     * ```
     * Values can be: scalar, null, DbQueryBuilderInterface (subquery), or ExpressionInterface
     *
     * @param mixed ...$values The values to insert
     * @return self Returns the command builder instance for method chaining
     * @throws InvalidArgumentException If the number of values doesn't match the number of fields
     */
    public function values(mixed ...$values): self;

    /**
     * Sets field-value pairs for a single-row insert using an associative array.
     *
     * Provides an alternative to fields() + values() for single-row inserts.
     * Multiple calls will override previous values, not add rows.
     *
     * Example:
     * ```php
     * $command->insert()
     *     ->into('users')
     *     ->set([
     *         'name' => 'John Doe',
     *         'email' => 'john@example.com',
     *         'status' => 'active'
     *     ]);
     * ```
     *
     * @param array<string, string|int|float|bool|null|DbQueryBuilderInterface|ExpressionInterface> $data
     * @return self Returns the command builder instance for method chaining
     */
    public function set(array $data): self;

    /**
     * Uses a SELECT query as the source for INSERT data.
     *
     * The SELECT query must return fields that match the fields specified
     * in the fields () method or match the table's field order if fields() is not used.
     *
     * Example:
     * ```php
     * $selectQuery = $query->select('name, email')
     *     ->from('temp_users')
     *     ->where('status')->equals('pending');
     *
     * $command->insert()
     *     ->into('users')
     *     ->fields('name', 'email')
     *     ->fromSelect($selectQuery);
     * ```
     *
     * @param DbQueryBuilderInterface $query The SELECT query providing the data
     * @return self Returns the command builder instance for method chaining
     */
    public function fromSelect(DbQueryBuilderInterface $query): self;

    /**
     * Updates fields when a duplicate key conflict occurs.
     *
     * Sets a single field to update when a duplicate key/unique constraint violation occurs.
     * Can be called multiple times to update multiple fields on conflict.
     *
     * Database-specific behavior:
     * - MySQL/MariaDB: ON DUPLICATE KEY UPDATE
     * - PostgresSQL: ON CONFLICT ... DO UPDATE SET
     * - SQLite: ON CONFLICT DO UPDATE SET
     *
     * Examples:
     * ```php
     * // Increment counter on duplicate
     * $command->insert()
     *     ->into('users')
     *     ->set(['email' => 'john@example.com', 'name' => 'John', 'login_count' => 1])
     *     ->onDuplicateKeyUpdate('login_count', new Expression ('login_count + 1'))
     *     ->onDuplicateKeyUpdate('last_login', new Expression ('NOW()'));
     *
     * // Update with scalar value
     * $command->insert()
     *     ->into('products')
     *     ->fields('sku', 'name', 'price')
     *     ->values('ABC-123', 'Widget', 19.99)
     *     ->onDuplicateKeyUpdate('name', 'Widget Updated')
     *     ->onDuplicateKeyUpdate('price', 24.99);
     * ```
     *
     * @param string $field The field to update on duplicate key
     * @param string|int|float|bool|null|ExpressionInterface $value The value to set (or Expression for complex logic)
     * @return self Returns the command builder instance for method chaining
     */
    public function onDuplicateKeyUpdate(
        string $field,
        string|int|float|bool|null|ExpressionInterface $value
    ): self;

    /**
     * Silently ignores duplicate key errors instead of failing.
     *
     * When a duplicate key/unique constraint violation occurs, the row is not inserted
     * and no error is raised. Useful for "insert if not exists" behavior.
     *
     * Database-specific behavior:
     * - MySQL: INSERT IGNORE INTO
     * - PostgresSQL: ON CONFLICT DO NOTHING
     * - SQLite: INSERT OR IGNORE INTO
     *
     * Examples:
     * ```php
     * // Single row - ignore if exists
     * $command->insert()
     *     ->into('users')
     *     ->set(['email' => 'john@example.com', 'name' => 'John'])
     *     ->orIgnore();
     *
     * // Multi-row insert - skip duplicates
     * $command->insert()
     *     ->into('tags')
     *     ->fields('name')
     *     ->values('php')
     *     ->values('mysql')
     *     ->values('laravel')
     *     ->orIgnore();
     * ```
     *
     * Note: Cannot be combined with onDuplicateKeyUpdate() or replace()
     *
     * @return self Returns the command builder instance for method chaining
     */
    public function orIgnore(): self;

    /**
     * Replaces existing row on duplicate key (DELETE then INSERT).
     *
     * When a duplicate key/unique constraint violation occurs, deletes the existing row
     * and inserts the new one. This is a DELETE + INSERT operation.
     *
     * Database-specific behavior:
     * - MySQL: REPLACE INTO
     * - PostgreSQL: Not directly supported (use DELETE + INSERT or ON CONFLICT)
     * - SQLite: INSERT OR REPLACE INTO
     *
     * Examples:
     * ```php
     * // Replace user if email exists
     * $command->insert()
     *     ->into('users')
     *     ->set([
     *         'email' => 'john@example.com',
     *         'name' => 'John Doe',
     *         'status' => 'active'
     *     ])
     *     ->replace();
     *
     * // Multi-row replace
     * $command->insert()
     *     ->into('cache')
     *     ->fields('key', 'value', 'expires')
     *     ->values('user:1', 'John', time() + 3600)
     *     ->values('user:2', 'Jane', time() + 3600)
     *     ->replace();
     * ```
     *
     * Warning: REPLACE deletes the old row, which may affect:
     * - Auto-increment values (new ID assigned)
     * - Foreign key references
     * - Triggers (DELETE + INSERT triggers fire)
     *
     * Note: Cannot be combined with onDuplicateKeyUpdate() or orIgnore()
     *
     * @return self Returns the command builder instance for method chaining
     */
    public function replace(): self;

    /**
     * Specifies which column(s) to check for conflicts (for PostgreSQL/SQLite style UPSERT).
     *
     * This method defines the constraint or columns that determine uniqueness for conflict resolution.
     * Must be followed by doUpdate() or doNothing() to define the conflict action.
     *
     * Database-specific behavior:
     * - PostgreSQL: ON CONFLICT (column) DO UPDATE/NOTHING
     * - SQLite: ON CONFLICT (column) DO UPDATE/NOTHING
     * - MySQL: Uses unique key detection automatically (converts to ON DUPLICATE KEY UPDATE)
     *
     * Examples:
     * ```php
     * // Conflict on single column
     * $command->insert()
     *     ->into('users')
     *     ->set(['email' => 'john@example.com', 'name' => 'John'])
     *     ->onConflict('email')
     *     ->doUpdate(['name' => 'John Updated']);
     *
     * // Conflict on multiple columns (composite unique constraint)
     * $command->insert()
     *     ->into('user_settings')
     *     ->set(['user_id' => 1, 'key' => 'theme', 'value' => 'dark'])
     *     ->onConflict('user_id', 'key')
     *     ->doUpdate(['value' => 'dark']);
     * ```
     *
     * Note: Cannot be combined with orIgnore(), replace(), or onDuplicateKeyUpdate()
     *
     * @param string ...$columnsOrConstraint Column name(s) or constraint name
     * @return self Returns the command builder instance for method chaining
     */
    public function onConflict(string ...$columnsOrConstraint): self;

    /**
     * Defines which fields to update when a conflict occurs.
     *
     * Must be called after onConflict(). Updates specified fields on conflict.
     * Can use ExpressionInterface for complex update logic.
     *
     * Examples:
     * ```php
     * // Update specific fields
     * $command->insert()
     *     ->into('users')
     *     ->set(['email' => 'john@example.com', 'name' => 'John', 'status' => 'active'])
     *     ->onConflict('email')
     *     ->doUpdate(['name' => 'John', 'status' => 'active']);
     *
     * // Update with expression
     * $command->insert()
     *     ->into('products')
     *     ->set(['sku' => 'ABC-123', 'stock' => 10])
     *     ->onConflict('sku')
     *     ->doUpdate([
     *         'stock' => new Expression('stock + 10'),
     *         'updated_at' => new Expression('NOW()')
     *     ]);
     * ```
     *
     * @param array<string, string|int|float|bool|null|ExpressionInterface> $fields Field-value pairs to update
     * @return self Returns the command builder instance for method chaining
     */
    public function doUpdate(array $fields): self;

    /**
     * Ignores conflicts (no insert, no update, no error).
     *
     * Must be called after onConflict(). When a conflict occurs on the specified
     * columns/constraint, the operation is silently skipped.
     *
     * Examples:
     * ```php
     * // Insert only if email doesn't exist
     * $command->insert()
     *     ->into('users')
     *     ->set(['email' => 'john@example.com', 'name' => 'John'])
     *     ->onConflict('email')
     *     ->doNothing();
     *
     * // Multi-row insert, skip existing
     * $command->insert()
     *     ->into('tags')
     *     ->fields('name')
     *     ->values('php')
     *     ->values('mysql')
     *     ->onConflict('name')
     *     ->doNothing();
     * ```
     *
     * @return self Returns the command builder instance for method chaining
     */
    public function doNothing(): self;
}
