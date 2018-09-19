Jasny Iterator
===

[![Build Status](https://travis-ci.org/jasny/iterator.svg?branch=master)](https://travis-ci.org/jasny/iterator)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jasny/iterator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jasny/iterator/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/jasny/iterator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jasny/iterator/?branch=master)
[![Packagist Stable Version](https://img.shields.io/packagist/v/jasny/iterator.svg)](https://packagist.org/packages/jasny/iterator)
[![Packagist License](https://img.shields.io/packagist/l/jasny/iterator.svg)](https://packagist.org/packages/jasny/iterator)

A set of useful [iterators](http://php.net/manual/en/class.iterator.php) for PHP.

Installation
---

    composer require jasny/iterator

Usage
---

**Callback**

* [MapIterator](#mapiterator)
* [MapKeyIterator](#mapkeyiterator)
* [ApplyIterator](#applyiterator)
* [UniqueIterator](#uniqueiterator)

**Sorting**

* [SortIteratorAggregate](#sortiteratoraggregate)
* [SortKeyIteratorAggregate](#sortkeyiteratoraggregate)

**Projection**

* [GroupIteratorAggregate](#groupiteratoraggregate)
* [FlattenIterator](#flatteniterator)
* [ProjectionIterator](#projectioniterator)

**Key/value**

* [ValueIterator](#valueiterator)
* [KeyIterator](#keyiterator)
* [CombineIterator](#combineiterator)
* [FlipIterator](#flipiterator)


### MapIterator

Map all elements of an Iterator. The keys remain unchanged.

```php
$persons = new \ArrayIterator([
    'client' => new Person("Max", 18),
    'seller' => new Person("Peter", 23),
    'lawyer' => new Person("Pamela", 23)
]);

$iterator = new MapIterator($persons, function(Person $value, string $key): string {
    return sprintf("%s = %s", $key, $value->name);
});
```

### MapKeyIterator

Map all keys elements of an Iterator. The values remain unchanged.

```php
$persons = new \ArrayIterator([
    'client' => new Person("Max", 18),
    'seller' => new Person("Peter", 23),
    'lawyer' => new Person("Pamela", 23)
]);

$iterator = new MapKeyIterator($persons, function(string $key, Person $value): string {
    return sprintf("%s (%s)", $value->name, $key);
});
```

_Caveat: The callback function switches value and key arguments, so the first argument is the key._

### ApplyIterator

Apply a callback to each element. This is a pass-through iterator, any value returned by the callback is ignored.

```php
$persons = new \ArrayIterator([
    'client' => new Person("Max", 18),
    'seller' => new Person("Peter", 23),
    'lawyer' => new Person("Pamela", 23)
]);

$iterator = new ApplyIterator($persons, function(Person $value, string $key): void {
    $value->role = $key;
});
```

### UniqueIterator

Filter to get only unique items. The keys are preserved, skipping duplicate values.

```php
$values = new \ArrayIterator(['foo', 'bar', 'qux', 'foo', 'zoo']);

$iterator = new UniqueIterator($values);
```

You can pass a callback, which should return a value. Filtering on distinct values will be based on that value.

```php
$persons = new \ArrayIterator([
    'client' => new Person("Max", 18),
    'seller' => new Person("Peter", 23),
    'lawyer' => new Person("Pamela", 23)
]);

$iterator = new UniqueIterator($persons, function(Person $value): int {
    return $value->age;
});
```

All values are stored for reference. The callback function can also be used to serialize and hash the value.

```php
$persons = new \ArrayIterator([
    'client' => new Person("Max", 18),
    'seller' => new Person("Peter", 23),
    'lawyer' => new Person("Pamela", 23)
]);

$iterator = new UniqueIterator($persons, function(Person $value): string {
    return hash('sha256', serialize($value));
});
```

The keys of an iterator don't have to be unique. This is unlike an associated array. You may return the key in the
callback to get distinct keys.

```php
$iterator = new UniqueIterator($someGenerator, function($value, $key) {
    return $key;
});
```

### SortIteratorAggregate

Sort all elements of an iterator.

```php
$values = new \ArrayIterator(["Charlie", "Echo", "Bravo", "Delta", "Foxtrot", "Alpha"]);

$iterator = new SortIteratorAggregate($values);
```

Instead of using the default sorting, a callback may be passed as user defined comparison function.

```php
$values = new \ArrayIterator(["Charlie", "Echo", "Bravo", "Delta", "Foxtrot", "Alpha"]);

$iterator = new SortIteratorAggregate($values, function($a, $b): int {
    return strlen($a) <=> strlen($b);
});
```

The keys are preserved.

_This is an `IteratorAggregate`. It may require traversing through all elements an putting them in an `ArrayIterator`
for sorting._

### SortKeyIteratorAggregate

Sort all elements of an iterator based on the key.

```php
$values = new \ArrayIterator([
    "Charlie" => "three",
    "Bravo" => "two",
    "Delta" => "four",
    "Alpha" => "one"
]);

$iterator = new SortKeyIteratorAggregate($values);
```

Similar to `SortIteratorAggregate`, a callback may be passed as user defined comparison function. 

_This is an `IteratorAggregate`. It may require traversing through all elements an putting them in an `ArrayIterator`
for sorting._

### ReverseIteratorAggregate

Reverse order of elements of an iterator.

```php
$values = new \ArrayIterator(["Charlie", "Echo", "Bravo", "Delta", "Foxtrot", "Alpha"]);

$iterator = new ReverseIteratorAggregate($values);
```

The keys are preserved.

_This is an `IteratorAggregate`. It may require traversing through all elements an putting them in an `ArrayIterator`
for sorting._

### GroupIteratorAggregate

Group elements of an iterator.

```php
$objects = new \ArrayIterator([
    (object)['type' => 'one'],
    (object)['type' => 'two'],
    (object)['type' => 'one'],
    (object)['type' => 'three'],
    (object)['type' => 'one'],
    (object)['type' => 'two']
]);

$iterator = new GroupIteratorAggregate($objects, function(\stdClass $object): string {
    return $object->type;
});
```

Alternatively, it's possible to group based on the key.

```php
$values = new \ArrayIterator([
    'alpha' => 'one',
    'bat' => 'two',
    'apple' => 'three',
    'cat' => 'four',
    'air' => 'five',
    'beast' => 'six'
]);

$iterator = new GroupIteratorAggregate($values, function(string $value, string $key): string {
    return substr($key, 0, 1);
});
```

_This is an `IteratorAggregate`. It requires traversing through all elements an putting them in a `CombineIterator` for
grouping._

### FlattenIterator

Walk through all sub-iterables and concatenate them.

```php
$values = new \ArrayIterator([
    ['one', 'two'],
    ['three', 'four', 'five'],
    [],
    ['six']
]);

$iterator = new FlattenIterator($values);
```

The entries may be an array, Iterator or IteratorAggregate. Other entries will not be flattened.

```php
$values = new \ArrayIterator([
    new \ArrayIterator(['one', 'two']),
    new \ArrayObject(['three', 'four', 'five']),
    new \EmptyIterator(),
    ['six'],
    'seven'
]);

$iterator = new FlattenIterator($values);
```

By default the keys are dropped, replaces by an incrementing counter (so as an numeric array). By passing `true` as
second parameters, the keys are remained.

```php
$values = new \ArrayIterator([
    ['one' => 'uno', 'two' => 'dos'],
    ['three' => 'tres', 'four' => 'cuatro', 'five' => 'cinco'],
    [],
    ['six' => 'seis']
]);

$iterator = new FlattenIterator($values, true);
```

### ProjectionIterator
Project each element of an iterator to an associated (or numeric) array. Each element should be an array or object.

For the projection, a mapping `[new key => old key]` must be supplied.

```php
$values = new \ArrayIterator([
    ['one' => 'uno', 'two' => 'dos', 'three' => 'tres', 'four' => 'cuatro', 'five' => 'cinco'],
    ['one' => 'yi', 'two' => 'er', 'three' => 'san', 'four' => 'si', 'five' => 'wu'],
    ['one' => 'één', 'two' => 'twee', 'three' => 'drie', 'four' => 'vier', 'five' => 'vijf']
]);

$mapping = ['I' => 'one', 'II' => 'two', 'II' => 'three', 'IV' => 'four'];

$iterator = new ProjectionIterator($values, $mapping);
```

If an element doesn't have a specified key, the value will be `null`.

The order of keys of the projected array is always the same as the order of the mapping.

The mapping may also be a numeric array.

```php
$values = new \ArrayIterator([
    ['one' => 'uno', 'two' => 'dos', 'three' => 'tres', 'four' => 'cuatro', 'five' => 'cinco'],
    (object)['three' => 'san', 'two' => 'er', 'five' => 'wu', 'four' => 'si'],
    new \ArrayObject(['two' => 'twee', 'four' => 'vier'])
]);

$mapping = ['one', 'three, 'four'];

$iterator = new ProjectionIterator($values, $mapping);
```

Scalar elements and `DateTime` object are ignored.

### ValueIterator

Keep the values, drop the keys. The keys become an incremental number. This is comparable to
[`array_values`](https://php.net/array_values).

```php
$values = new \ArrayIterator(['one' => 'uno', 'two' => 'dos', 'three' => 'tres', 'four' => 'cuatro']);

$spanish = new ValueIterator($values);
```

### KeyIterator

Use the keys as values. The keys become an incremental number. This is comparable to
[`array_keys`](https://php.net/array_keys).

```php
$values = new \ArrayIterator(['one' => 'uno', 'two' => 'dos', 'three' => 'tres', 'four' => 'cuatro']);

$english = new KeyIterator($values);
```

### CombineIterator

Iterator through keys and values.

```php
$english = new \ArrayIterator(['one', 'two', 'three', 'four']);
$spanish = new \ArrayIterator(['uno', 'dos', 'tres', 'cuatro']);

$iterator = new CombineIterator($english, $spanish);
```

The key may be any type and doesn't need to be unique.

```php
$keys = new \ArrayIterator([null, new \stdClass(), 'foo', ['hello', 'world'], 5.2, 'foo']);
$values = new \ArrayIterator(['one', 'two', 'three', 'four', 'five', 'six']);

$iterator = new CombineIterator($keys, $values);
```

The number of elements yielded from the iterator only depends on the number of keys. If there are more keys than
values, the value defaults to `null`. If there are more values than keys, the additional values are not returned.

### FlipIterator

Use values as keys and visa versa.

```php
$values = new \ArrayIterator(['one' => 'uno', 'two' => 'dos', 'three' => 'tres', 'four' => 'cuatro']);

$iterator = new FlipIterator($values);
```

Both the value and key may be any type and don't need to be unique.
