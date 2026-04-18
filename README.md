![Auth Gate](http://public-assets.andrewdyer.rocks/images/covers/auth-gate.png)

<p align="center">
  <a href="https://packagist.org/packages/andrewdyer/auth-gate"><img src="https://poser.pugx.org/andrewdyer/auth-gate/v/stable?style=for-the-badge" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/andrewdyer/auth-gate"><img src="https://poser.pugx.org/andrewdyer/auth-gate/downloads?style=for-the-badge" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/andrewdyer/auth-gate"><img src="https://poser.pugx.org/andrewdyer/auth-gate/license?style=for-the-badge" alt="License"></a>
  <a href="https://packagist.org/packages/andrewdyer/auth-gate"><img src="https://poser.pugx.org/andrewdyer/auth-gate/require/php?style=for-the-badge" alt="PHP Version Required"></a>
</p>

<p align="center">
  Built on top of <a href="https://github.com/andrewdyer/php-package-template">andrewdyer/php-package-template</a>
</p>

# Auth Gate

A framework-agnostic PHP library for defining and enforcing authorisation rules through a simple, expressive gate interface.

## Introduction

This library provides a lightweight, dependency-free mechanism for registering ability callbacks and evaluating them against an authenticated actor. It supports before-hooks for global overrides, multiple ability checks, and throws a typed exception when authorisation fails, making it straightforward to integrate into any PHP application regardless of framework.

## Prerequisites

- **[PHP](https://www.php.net/)**: Version 8.3 or higher is required.
- **[Composer](https://getcomposer.org/)**: Dependency management tool for PHP.

## Installation

```bash
composer require andrewdyer/auth-gate
```

## Getting Started

Implement the `Authenticatable` interface on the actor class, then create a `Gate` instance with that actor.

1. Implement the `Authenticatable` interface:

   ```php
   use AndrewDyer\Gate\Authenticatable;

   class User implements Authenticatable
   {
       public function __construct(public readonly int $id) {}
   }
   ```

2. Create a `Gate` instance:

   ```php
   use AndrewDyer\Gate\Gate;

   $user = new User(1);
   $gate = new Gate($user);
   ```

## Usage

The following examples demonstrate the available gate operations using the setup above.

### Defining Abilities

Abilities are registered via the `define` method, which accepts an ability name and a callback that returns a boolean.

```php
$gate->define('edit-post', function ($user, $post) {
    return $user->id === $post->authorId;
});
```

### Checking Abilities

Use `allows` and `denies` to evaluate a single ability, or `all` and `any` for multiple abilities.

```php
$gate->allows('edit-post', $post); // true or false
$gate->denies('edit-post', $post); // true or false

$gate->all(['edit-post', 'delete-post'], $post);  // true if all pass
$gate->any(['edit-post', 'view-post'], $post);    // true if any pass
```

### Authorising Actions

`authorize` throws an `UnauthorizedException` if the actor lacks any of the given abilities.

```php
use AndrewDyer\Gate\UnauthorizedException;

try {
    $gate->authorize(['edit-post'], $post);
} catch (UnauthorizedException $e) {
    // Actor is not authorised
}
```

### Registering Before Callbacks

Before callbacks run prior to all ability checks. Returning `true` or `false` short-circuits the evaluation; returning `null` (or nothing) defers to the defined ability.

```php
$gate->before(function ($user, $ability) {
    if ($user->isAdmin()) {
        return true;
    }
});
```

## License

Licensed under the [MIT license](https://opensource.org/licenses/MIT) and is free for private or commercial projects.
