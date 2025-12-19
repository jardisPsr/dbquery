# DbQuery - Database Query Builder Interfaces

![Build Status](https://github.com/JardisPsr/dbquery/actions/workflows/ci.yml/badge.svg)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.2-blue.svg)](https://www.php.net/)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-Level%208-success.svg)](phpstan.neon)
[![PSR-4](https://img.shields.io/badge/autoload-PSR--4-blue.svg)](https://www.php-fig.org/psr/psr-4/)
[![PSR-12](https://img.shields.io/badge/code%20style-PSR--12-orange.svg)](phpcs.xml)

A comprehensive set of **interface-only** database query builder contracts for domain-driven design. This library provides the blueprints for building type-safe, fluent SQL query builders - you implement the interfaces according to your specific needs.

> **Note**: This is an interface library only. It contains no implementations, just contracts that define how query builders should work.

## Installation

```bash
composer require jardispsr/dbquery
```

## Requirements

- PHP >= 8.2
- PDO extension

## Why Use This Library?

DbQuery provides a clean, professional foundation for building your own query builders:

- **Interface-First Design**: Define contracts before implementation, enabling flexible architecture
- **Domain-Driven Design**: Follows SOLID principles with interface segregation
- **Multi-Dialect Support**: Design for MySQL, PostgreSQL, and SQLite from the start
- **Type Safety**: Leverage PHP 8.2+ type system for compile-time safety
- **Production-Ready Standards**: PHPStan level 8, PSR-12 compliant, fully documented

## Key Features

- **Fluent Interface Pattern**: Chainable methods for intuitive, readable query building
- **Comprehensive Query Support**: SELECT, INSERT, UPDATE, DELETE with full feature sets
- **Advanced SQL Features**:
  - Common Table Expressions (CTEs), including recursive CTEs
  - Window functions with PARTITION BY and custom framing
  - JSON path extraction and contains operations
  - Subqueries in SELECT, WHERE, JOIN, and INSERT...SELECT
  - Upsert operations with conflict resolution
- **Prepared Statements**: Built-in parameter binding for SQL injection prevention
- **Clean Architecture**: Small, focused interfaces composed into larger ones

## Architecture

### Interface Hierarchy

The library uses **Interface Segregation Principle** - small, focused interfaces that compose into larger ones:

#### Core Query Builders

These extend multiple feature interfaces to provide complete query building capabilities:

- **`DbQueryBuilderInterface`** - SELECT queries
  - Extends: `DbWhereConditionInterface`, `DbJoinInterface`, `DbQueryExistsInterface`, `DbOrderLimitInterface`, `DbSqlGeneratorInterface`
  - Methods: `select()`, `from()`, `selectWindow()`, `with()`, `withRecursive()`, `union()`, `groupBy()`, `having()`

- **`DbInsertBuilderInterface`** - INSERT operations
  - Extends: `DbSqlGeneratorInterface`
  - Methods: `insert()`, `into()`, `fields()`, `values()`, `onConflict()`, `doUpdate()`, `doNothing()`

- **`DbUpdateBuilderInterface`** - UPDATE operations
  - Extends: `DbWhereConditionInterface`, `DbQueryExistsInterface`, `DbJoinInterface`, `DbOrderLimitInterface`, `DbSqlGeneratorInterface`
  - Methods: `update()`, `table()`, `set()`

- **`DbDeleteBuilderInterface`** - DELETE operations
  - Extends: `DbWhereConditionInterface`, `DbQueryExistsInterface`, `DbJoinInterface`, `DbOrderLimitInterface`, `DbSqlGeneratorInterface`
  - Methods: `delete()`, `from()`

#### Feature Interfaces

Mix-in interfaces for specific capabilities:

- **`DbWhereConditionInterface`** - WHERE clause building with AND/OR logic
- **`DbQueryConditionBuilderInterface`** - Standard comparison operators
- **`DbComparisonOperatorsInterface`** - Common operators (equals, greater, like, in, between, etc.)
- **`DbQueryJsonConditionBuilderInterface`** - JSON operations (extract, contains)
- **`DbQueryExistsInterface`** - EXISTS and NOT EXISTS subqueries
- **`DbJoinInterface`** - INNER and LEFT JOIN operations
- **`DbOrderLimitInterface`** - ORDER BY, LIMIT, OFFSET clauses
- **`DbWindowBuilderInterface`** - Window function specifications

#### Supporting Interfaces

- **`DbSqlGeneratorInterface`** - Generate SQL with dialect parameter ('mysql', 'pgsql', 'sqlite')
- **`DbPreparedQueryInterface`** - Prepared query result with SQL and parameter bindings
- **`ExpressionInterface`** - Raw SQL expressions for special cases

## Usage Examples

> **Remember**: These are interface definitions. The examples show how code would look when using implementations of these interfaces.

### Basic SELECT Query

```php
// Build a query with conditions and ordering
$query = $builder
    ->select('id, name, email')
    ->from('users')
    ->where('status')->equals('active')
    ->and('age')->greater(18)
    ->orderBy('name', 'ASC')
    ->limit(10);

// Generate prepared statement for MySQL
$prepared = $query->sql('mysql', true);
// Returns DbPreparedQueryInterface with SQL and bindings
```

### INSERT with Upsert (Conflict Resolution)

```php
// INSERT with ON CONFLICT handling (PostgreSQL style)
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
// Update based on joined table data
$update = $builder
    ->update()
    ->table('users', 'u')
    ->innerJoin('orders', 'orders.user_id = u.id', 'o')
    ->set('u.last_order_date', new Expression('MAX(o.created_at)'))
    ->where('o.status')->equals('completed');

$prepared = $update->sql('mysql', true);
```

### Common Table Expressions (CTEs)

```php
// Regular CTE
$query = $builder
    ->with('active_users', $subquery)  // $subquery is DbQueryBuilderInterface
    ->select('*')
    ->from('active_users')
    ->where('registration_date')->greater('2024-01-01');

// Recursive CTE for hierarchical data
$query = $builder
    ->withRecursive('hierarchy', $anchorQuery, $recursiveQuery)
    ->select('*')
    ->from('hierarchy');

$prepared = $query->sql('pgsql', true);
```

### Window Functions

```php
// Partition data and apply window functions
$query = $builder
    ->select('id, name, department, salary')
    ->selectWindow('ROW_NUMBER', 'row_num')
        ->partitionBy('department')
        ->windowOrderBy('salary', 'DESC')
        ->endWindow()
    ->selectWindow('AVG', 'dept_avg_salary')
        ->windowFunction('salary')
        ->partitionBy('department')
        ->endWindow()
    ->from('employees');

$prepared = $query->sql('mysql', true);
```

### JSON Operations

```php
// Query JSON columns
$query = $builder
    ->select('*')
    ->from('users')
    ->whereJson('metadata')->extract('$.role')->equals('admin')
    ->andJson('settings')->contains('notifications', '$.features');

$prepared = $query->sql('mysql', true);
```

### Complex Query with Subqueries

```php
// Subquery in SELECT
$avgSalarySubquery = $builder
    ->select('AVG(salary)')
    ->from('employees', 'e2')
    ->where('e2.department')->equalsColumn('e1.department');

$query = $builder
    ->select('e1.name, e1.salary')
    ->selectSubquery($avgSalarySubquery, 'dept_avg')
    ->from('employees', 'e1')
    ->where('e1.salary')->greater($avgSalarySubquery);

$prepared = $query->sql('mysql', true);
```

## Development

This project uses Docker for all development tasks. See [CLAUDE.md](.claude/CLAUDE.md) for comprehensive development documentation.

### Quick Start

```bash
# Install dependencies
make install

# Run coding standards check
make phpcs

# Run static analysis
make phpstan

# Update dependencies
make update
```

### Code Quality

- **Standards**: PSR-12 with 120 character line limit
- **Static Analysis**: PHPStan level 8 (strictest)
- **Pre-commit Hooks**: Automatically enforces coding standards and branch naming conventions
- **CI/CD**: GitHub Actions runs PHPCS and PHPStan on all PRs

### Branch Naming Convention

Branches must follow the pattern: `feature/123456_description`, `fix/1234567_description`, or `hotfix/123456_description`

### Docker Environment

All commands run through Docker Compose - never run PHP commands directly on the host:

```bash
make shell      # Open interactive shell in container
make remove     # Clean up Docker environment
```

All interfaces must include:
- `declare(strict_types=1);` declaration
- Comprehensive PHPDoc comments with examples
- Full type declarations for all parameters and return types

## License

MIT License - see [LICENSE](LICENSE) file for details

## Authors

**Jardis Core Development**
- Email: jardisCore@headgent.dev
- GitHub: [@JardisPsr](https://github.com/JardisPsr)

## Support

- **Issues**: https://github.com/JardisPsr/dbquery/issues
- **Email**: jardisCore@headgent.dev


**Built with ❤️ by Jardis Core Development**
