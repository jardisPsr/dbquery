<?php

declare(strict_types=1);

namespace JardisPsr\DbQuery;

/**
 * Interface for JSON-specific query condition building
 *
 * This interface provides methods for constructing JSON-specific conditions
 * such as path extraction, containment checks, and length operations.
 *
 * Standard comparison operators (equals, greater, in, like, etc.) are inherited
 * from DbComparisonOperatorsInterface and work with JSON-extracted values.
 * This interface adds JSON-specific operations like path extraction, containment
 * checks, and length queries.
 *
 * @package JardisPsr\Contract\Database
 */
interface DbQueryJsonConditionBuilderInterface extends DbComparisonOperatorsInterface
{
    /**
     * Extracts a value from JSON using a path.
     *
     * This method is typically chained with comparison operators to test
     * extracted JSON values.
     *
     * Examples:
     * ```php
     * // Extract and compare age
     * $query->whereJson('metadata')->extract('$.age')->greater(18);
     *
     * // Extract nested property
     * $query->whereJson('settings')->extract('$.user.preferences.theme')->equals('dark');
     * ```
     *
     * @param string $path JSON path expression (e.g., '$.age', '$.address.city', '$.items[0]')
     * @return self For method chaining with comparison operators
     */
    public function extract(string $path): self;

    /**
     * Checks if JSON contains a specific value.
     *
     * Searches for a value within a JSON array or checks for the existence
     * of a key-value pair in a JSON object.
     *
     * Examples:
     * ```php
     * // Check if array contains value
     * $query->whereJson('tags')->contains('php');
     *
     * // Check within a specific path
     * $query->whereJson('data')->contains('admin', '$.roles');
     *
     * // Check object contains key-value pair (database-specific)
     * $query->whereJson('settings')->contains(['theme' => 'dark']);
     * ```
     *
     * @param mixed $value The value to search for (scalar or array for key-value pairs)
     * @param string|null $path Optional JSON path to search within
     * @param string|null $closeBracket Optional closing bracket(s)
     * @return DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface The builder for method chaining
     */
    public function contains(
        mixed $value,
        ?string $path = null,
        ?string $closeBracket = null
    ): DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface;

    /**
     * Checks if JSON does NOT contain a specific value.
     *
     * Inverse of contains() - checks that a value is absent from a JSON array
     * or a key-value pair doesn't exist in a JSON object.
     *
     * Examples:
     * ```php
     * // Ensure tag is not present
     * $query->whereJson('tags')->notContains('deprecated');
     *
     * // Check within a specific path
     * $query->whereJson('permissions')->notContains('delete', '$.actions');
     * ```
     *
     * @param mixed $value The value to check absence of
     * @param string|null $path Optional JSON path
     * @param string|null $closeBracket Optional closing bracket(s)
     * @return DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface The builder for method chaining
     */
    public function notContains(
        mixed $value,
        ?string $path = null,
        ?string $closeBracket = null
    ): DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface;

    /**
     * Gets the length of a JSON array or number of keys in a JSON object.
     *
     * This method is typically chained with comparison operators to test
     * the size of JSON collections.
     *
     * Examples:
     * ```php
     * // Check array has at least 3 items
     * $query->whereJson('tags')->length()->greaterEquals(3);
     *
     * // Check specific path length
     * $query->whereJson('data')->length('$.items')->equals(0);
     *
     * // Check object has properties
     * $query->whereJson('settings')->length()->greater(0);
     * ```
     *
     * @param string|null $path Optional JSON path to measure length of specific nested element
     * @return self For method chaining with comparison operators
     */
    public function length(?string $path = null): self;
}
