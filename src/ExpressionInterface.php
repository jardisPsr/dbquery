<?php

declare(strict_types=1);

namespace JardisPsr\DbQuery;

/**
 * Represents a raw SQL expression that should not be escaped or quoted
 *
 * Used for:
 * - Arithmetic expressions (e.g., "count + 1")
 * - Column references (e.g., "users.id")
 * - SQL functions (e.g., "NOW()")
 * - Complex expressions (e.g., "CASE WHEN ... END")
 */
interface ExpressionInterface
{
    /**
     * Get the raw SQL expression string
     *
     * @return string The unescaped SQL expression
     */
    public function toSql(): string;
}
