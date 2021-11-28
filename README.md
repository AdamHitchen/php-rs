# PhpRs - WIP

PhpRs aims to provide parts of the rust std in php.

Currently, PhpRs provides a partial implementation of Rust's Option and Iterators.

Proper documentation will come later.

## Option
Options wrap a value with the Option type.
Options can either be Some or None.
When working with options null is considered to be an invalid value. Any time null is encountered the program will panic.

The only exception to this is the Option::from method - provided as a convenience to construct values which may be null.

```php
use PhpRs\Option;

$option = Option::from(123);
assert($option == Option::Some(123));

$option = Option::from(null);
assert($option === Option::None())
```

The use of Option forces you to check that the value is actually valid.
If you do not ensure the value is valid before attempting to retrieve it from the option the program may panic. 
Values are primarily retrieved from Options by using the `unwrap` or `expect` methods. 

```php
use PhpRs\Option;

$option = Option::Some(123);
assert(123, $option->unwrap());

$none = Option::None();
// EXITS! 
$none->unwrap();
```

Usually you want to check the state of the Option with `$option->isSome()` or `$option->isNone()`

```php
use PhpRs\Option;

$option = Option::from(rand(1, 10) > 5 ? 1 : null);
if ($option->isSome()) {
    echo $option->unwrap();
} else {
    echo "Nothing!";
}
```

There are many convenience methods provided with Options to make them safer to work with.
 Check the ~~documentation~~ (WIP) to learn how to work with them idiomatically.

## Result
WIP - not yet available

## Iterators

Iterators allow you to easily filter, apply transformations to, and consume iterables. Iterators are lazy - no processing will be done until the iterator is consumed using a method like `collect` or `find`.
Iterators can be used with any `iterable` value, such as classes implementing `\Iterator` or a basic array.
```php
use PhpRs\Iterator;

$iter = Iterator::from(["Hello", ", ", "World!"]);

foreach ($iter as $value) {
    echo $value;
}
```

### Examples
#### Filter and transform a list
```php
$array = Iterator::from(range(1, 10))
            // Filter out odd numbers
            ->filter(fn (int $value): bool => $value % 2 === 0)
            // Transform the value from int to string
            ->map(fn (int $value): string => (string) $value)
            // Collect the values into an array - above transformations were not applied until now
            ->collect();
```

#### Search for a value
```php
/* @var Option<int> $value */
$value = Iterator::from(range(1, 10))
            // Return the first value matching the condition.
            // Values are returned as an Option - if no value matches None is returned.
            ->find(fn (int $value): bool => $value > 9);

assert($value = Option::Some(10));
```

#### Skip some values
```php
$value = Iterator::from(range(1, 10))
            ->skip(5)
            ->collect();

assert($value === [6, 7, 8, 9, 10]);
```

#### Combine two iterators
```php
$value = Iterator::from([1, 2, 3,])
            ->chain([4, 5, 6])
            ->collect();

assert($value === [1, 2, 3, 4, 5, 6]);
```


## Panics

When error conditions are encountered such as calling `unwrap()` on a `None` value the library will cause a panic.
Essentially this means the program will call trigger_error and exit() with an error code and your application will halt.

This may seem heavy-handed, but the purpose is to ensure business logic does not run with invalid values.
Null values are often not handled properly, by instead causing a panic we can make such bugs easier to identify.

By registering a callback to the panic handler ahead of time you can handle the error message and stack trace.
Alternatively use `set_error_handler`. 


```php
    \PhpRs\Panic\Panic::registerPanicCallback(function (string $message) {
        Logger::logException($message);
    });
```