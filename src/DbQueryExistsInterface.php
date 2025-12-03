<?php

declare(strict_types=1);

namespace JardisPsr\DbQuery;

interface DbQueryExistsInterface
{
    /**
     * Adds an EXISTS condition to the query.
     *
     * @param DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface $query The query instance
     * @param string|null $closeBracket An optional closing bracket to be appended to the query for nested conditions.
     * @return DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface The modified query instance
     */
    public function exists(
        DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface $query,
        ?string $closeBracket = null
    ): DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface;

    /**
     * Adds a NOT EXISTS condition to the query.
     *
     * @param DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface $query The query instance
     * @param string|null $closeBracket An optional closing bracket to be appended to the query for nested conditions.
     * @return DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface The modified query instance
     */
    public function notExists(
        DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface $query,
        ?string $closeBracket = null
    ): DbQueryBuilderInterface|DbUpdateBuilderInterface|DbDeleteBuilderInterface;
}
