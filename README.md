# DbQuery Interfaces

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.2-blue.svg)](https://www.php.net/)

This package provides database query builder interfaces for a domain-driven design approach.

## Installation

```bash
composer require jardispsr/dbquery
```

## Requirements

- PHP >= 8.2
- PDO extension

## Description

This library defines a comprehensive set of interfaces for building SQL queries using a fluent interface pattern. It supports multiple SQL dialects (MySQL, PostgreSQL, SQLite) and provides type-safe, expressive query construction.

## Key Features

- **Fluent Interface**: Chainable methods for intuitive query building
- **Multiple SQL Dialects**: Support for MySQL, PostgreSQL, and SQLite
- **Type Safety**: Full PHP 8.2+ type declarations
- **DDD-Oriented**: Clean interface segregation following SOLID principles
- **Prepared Statements**: Built-in support for safe parameter binding
- **Advanced Features**: CTEs, window functions, JSON operations, subqueries

## Interfaces Overview

### Core Query Builders

- **`DbQueryBuilderInterface`**: SELECT queries with full feature support
- **`DbInsertBuilderInterface`**: INSERT operations with upsert capabilities
- **`DbUpdateBuilderInterface`**: UPDATE operations with JOIN support
- **`DbDeleteBuilderInterface`**: DELETE operations with conditions

### Condition Building

- **`DbWhereConditionInterface`**: WHERE, AND, OR conditions
- **`DbQueryConditionBuilderInterface`**: Standard comparison operators
- **`DbComparisonOperatorsInterface`**: Common operators (equals, greater, like, etc.)
- **`DbQueryJsonConditionBuilderInterface`**: JSON-specific conditions
- **`DbQueryExistsInterface`**: EXISTS and NOT EXISTS conditions

### Additional Features

- **`DbJoinInterface`**: INNER and LEFT JOIN operations
- **`DbOrderLimitInterface`**: ORDER BY and LIMIT clauses
- **`DbWindowBuilderInterface`**: Window functions and specifications
- **`DbSqlGeneratorInterface`**: SQL generation with dialect support
- **`DbPreparedQueryInterface`**: Prepared query with bindings
- **`ExpressionInterface`**: Raw SQL expressions

## Usage Examples

### Basic SELECT Query

```php
$query = $builder
    ->select('id, name, email')
    ->from('users')
    ->where('status')->equals('active')
    ->and('age')->greater(18)
    ->orderBy('name', 'ASC')
    ->limit(10);

$prepared = $query->sql('mysql', true);
```

### INSERT with Conflict Resolution

```php
$insert = $builder
    ->insert()
    ->into('users')
    ->fields('email', 'name', 'status')
    ->values('john@example.com', 'John Doe', 'active')
    ->onConflict('email')
    ->doUpdate(['name' => 'John Doe', 'status' => 'active']);

$prepared = $insert->sql('pgsql', true);
```

### UPDATE with JOIN

```php
$update = $builder
    ->update()
    ->table('users', 'u')
    ->innerJoin('orders', 'orders.user_id = u.id', 'o')
    ->set('u.last_order_date', new Expression('MAX(o.created_at)'))
    ->where('o.status')->equals('completed');

$prepared = $update->sql('mysql', true);
```

### Window Functions

```php
$query = $builder
    ->select('id, name, department, salary')
    ->selectWindow('ROW_NUMBER', 'row_num')
        ->partitionBy('department')
        ->windowOrderBy('salary', 'DESC')
        ->endWindow()
    ->from('employees');

$prepared = $query->sql('mysql', true);
```

### JSON Operations

```php
$query = $builder
    ->select('*')
    ->from('users')
    ->whereJson('metadata')->extract('$.role')->equals('admin')
    ->andJson('settings')->contains('notifications', '$.features');

$prepared = $query->sql('mysql', true);
```

## License

MIT

## Authors

- Jardis Core Development <jardisCore@headgent.dev>

## Support

- Issues: https://github.com/JardisPsr/dbquery/issues
- Email: jardisCore@headgent.dev

## Homepage

https://github.com/JardisPsr/dbquery
