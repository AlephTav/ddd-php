# DDD Tools
Collection of classes that can be useful to build applications according to Domain Driven Design (DDD).

## Table of Contents

- [Infrastructure Layers](#infrastructure)
    - [Base Domain Classes](#base_classes)
        - [DTO](#dto)
        - [DomainObject](#DomainObject)
        - [ValueObject](#ValueObject)
    - [Enums](#enums)
    - [Helpers](#helpers)
    - [SQL Builder](#sql_builder)
        - [MySQL](#mysql)
            - [Select Queries](#mysql_select)
        - [PostgreSQL](#postgresql)
- [Model Layer](#model)
    - [Entity Identities](#identities)
    - [Common Value Objects](#value_objects)
    - [Common Domain Exceptions](#exceptions)
    - [Common Domain Events](#events)
- [Application Layers](#application)
    - [Base Application Service](#application_service)
    - [Base Query Service](#query_service)
    - [Domain Event Subscribers](#event_subscribers)
    
### Base Domain Classes

#### DTO

The base class of all domain objects is `DTO`. DTO allows to embed properties (as in JAVA, C# and some other
strictly-typed program languages) in our classes. A class property consists of three elements:
1. Private or protected class field.
1. Property description according to PHPDoc (@property) at the class comment.
1. Optional setter or getter.

`DTO` is an abstract class. There are two child classes of it: `StrictDto` and `WeakDto`. The difference between them is 
that the first one throws `NonExistentPropertyException` during class initialization if a property does not exist and 
the second one isn't.

DTO usage example:
```php
/**
 * @property string $prop1
 * @property-read DateTime $prop2
 * @property-write int $prop3
 */
class Data extends StrictDto
{
    private $prop1;
    private DateTime $prop2;
    protected int $prop3 = 0;
    
    public function setProp1(string $value): void
    {
        $this->prop1 = $value;
    }
    
    protected function setProp2(?DateTime $value): void
    {
        $this->prop2 = $value;
    }
    
    public function getProp2(): array
    {
        return $this->prop2 ?: new DateTime();
    }
}

$dto = new Data([
    'prop1' => 'abc',
    'prop2' => null,
    'prop3' => 10,
    'prop4' => 1 // causes NonExistentPropertyException for StrictDto but not for WeakDto.
]);

$val1 = $dto->prop1; // abc
$val2 = $dto->prop2; // current time object
$dto->prop3 = 100;
$val3 = $dto->prop3  // throws RuntimeException
$dto->prop4 = 1      // throws NonExistentPropertyException

$dtoAsArray = $dto->toArray(); // you can use toNestedArray() if some properties are DTO themselves.
$dtoAsJson = json_encode($dto); // or use $dto->toJson()
$dtoAsString = (string)$dto // or use $dto->toString() 
``` 

There are some rules to get properties work properly:
- The property type in PHPDoc comment does not matter. To restrict property by type you need to specify type hinting for 
property setter, getter or field. If assigned value does not match property type `InvalidArgumentException` is thrown.
- Getters of read-only or regular properties must be public.
- Setters of write-only or regular properties must be public.
- Private and protected setters are automatically invoked during the class initialization.
- If you want to inherit a property its field and setter must not be private.

#### DomainObject

`DomainObject` is a base class for all domain object. It is inherited from `StrictDto`. All domain objects contains sone
useful methods:
1. `equals()` to compare domain objects with others.
1. `copy()` to create a copy of the domain object.
1. `copyWith()` to create a copy of the domain object with the given property values.
1. `hash()` to get unique domain object hash.
1. `domainName()` to get name of the domain object (it equals class name by default).

#### ValueObject

`ValueObject` is a base class for all domain value objects. It has the same methods as `DomainObject`.
    
### SQL Builder

SQL Builder is a simple wrapper that allows to build SQL query string for some particular RDBMS (MySQL and PostgreSQL 
are currently only supported). You can use it to be independent of any PHP framework. See the table below to figure out 
how to use SQL Builder.

#### MySQL

##### Select Queries

PHP Expression:
```php
$row = (new SelectQuery($queryExecutor))
    ->from('users', 'u')
    ->where('u.id', '=', 10)
    ->row();
    
// $row is a single record (associative array) or empty array.
```

Executed SQL Query:
```MySQL
SELECT * FROM users u 
WHERE u.id = 10
```

PHP Expression:
```php
$rows = (new SelectQuery($queryExecutor))
    ->from('users')
    ->select([
        'id',
        'firstName',
        'lastName',
        'email'
    ])
    ->where('email', 'LIKE', '%gmail.com')
    ->rows();
    
// $rows is a record set (array of associative arrays) or empty array. 
```

Executed SQL Query:
```MySQL
SELECT id, firstName, lastName, email FROM users 
WHERE email LIKE '%gmail.com'
```

PHP Expression:
```php
$rows = (new SelectQuery($queryExecutor))
    ->from([
        'users' => 'u,
        'contacts' => 'c'
    ])
    ->select([
        'u.id' => 'user_id',
        'c.id' => 'contact_id'
    ])
    ->where('c.user_id = u.id')
    ->limit(10)
    ->offset(50)
    ->rows();
    
// $rows is a record set (array of associative arrays) or empty array.
```

Executed SQL Query:
```MySQL
SELECT u.id user_id, c.id contact_id FROM users u, contacts c 
WHERE c.user_id = u.id 
LIMIT 10 OFFSET 50
```

PHP Expression:
```php
$rows = (new SelectQuery($queryExecutor))
    ->from('users', 'u)
    ->from('contacts', 'c')
    ->select('u.id', 'user_id')
    ->select('c.id', 'contact_id')
    ->where('c.user_id = u.id')
    ->rows();
    
// $rows is a record set (array of associative arrays) or empty array.
```

Executed SQL Query:
```MySQL
SELECT u.id user_id, c.id contact_id FROM users u, contacts c 
WHERE c.user_id = u.id
```

PHP Expression:
```php
$rows = (new SelectQuery($queryExecutor))
    ->from('users')
    ->orderBy('email', 'DESC')
    ->orderBy('id', 'ASC')
    ->rows();
    
// $rows is a record set (array of associative arrays) or empty array.
```

Executed SQL Query:
```MySQL
SELECT * FROM users
ORDER BY email DESC, id ASC 
```

PHP Expression:
```php
$count = (new SelectQuery($queryExecutor))
    ->from('users u')
    ->leftJoin('contacts c', 'c.user_id = u.id')
    ->where('u.id', '=', 5)
    ->count('DISTINCT c.name');
    
// $count is an integer value.
```

Executed SQL Query:
```MySQL
SELECT COUNT(DISTINCT c.name) FROM users u
LEFT JOIN contacts c ON c.user_id = u.id
WHERE u.id = 5
```

PHP Expression:
```php
$count = (new SelectQuery($queryExecutor))
    ->from('users u')
    ->innerJoin('contacts c', 'c.user_id = u.id')
    ->select([
        'u.id', 
        'u.email'
    ])
    ->select('COUNT(c.id)', 'contact_number')
    ->groupBy('u.id')
    ->rows();
    
// $rows is a record set (array of associative arrays) or empty array.
```

Executed SQL Query:
```MySQL
SELECT i.id, u.email, COUNT(c.id) contact_number FROM users u
INNER JOIN contacts c ON c.user_id = u.id
GROUP BY u.id
```

#### PostgreSQL
