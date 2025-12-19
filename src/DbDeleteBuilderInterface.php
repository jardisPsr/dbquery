<?php

declare(strict_types=1);

namespace JardisPsr\DbQuery;

/**
 * Interface for building SQL DELETE commands using a fluent interface pattern.
 *
 * This interface provides methods to construct DELETE queries programmatically,
 * supporting WHERE conditions, JOINs (for some dialects), LIMIT, and ORDER BY.
 *
 * @package JardisPsr\Contract\Database
 */
interface DbDeleteBuilderInterface extends
    DbWhereConditionInterface,
    DbQueryExistsInterface,
    DbJoinInterface,
    DbOrderLimitInterface,
    DbSqlGeneratorInterface
{
    /**
     * Specifies the table to delete from.
     *
     * This method defines the target table for the DELETE operation.
     * An optional alias can be provided for use in JOINs or complex conditions.
     *
     * Example:
     * ```php
     * $command->delete()->from('users', 'u');
     * ```
     *
     * @param string $container The table name
     * @param string|null $alias Optional alias for the table
     * @return self Returns the command builder instance for method chaining
     */
    public function from(string $container, ?string $alias = null): self;
}
