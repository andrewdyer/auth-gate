# Auth Gate

A framework-agnostic gate for defining abilities and enforcing authorisation checks against an authenticated user.

[![Latest Stable Version](http://poser.pugx.org/andrewdyer/auth-gate/v?style=flat-square)](https://packagist.org/packages/andrewdyer/auth-gate)
[![Total Downloads](http://poser.pugx.org/andrewdyer/auth-gate/downloads?style=flat-square)](https://packagist.org/packages/andrewdyer/auth-gate)
[![License](http://poser.pugx.org/andrewdyer/auth-gate/license?style=flat-square)](https://packagist.org/packages/andrewdyer/auth-gate)
[![PHP Version Require](http://poser.pugx.org/andrewdyer/auth-gate/require/php?style=flat-square)](https://packagist.org/packages/andrewdyer/auth-gate)

## Introduction

This library lets you register named ability callbacks and evaluate them against the authenticated user who is performing the action. It supports global before callbacks for overrides, checks for single or multiple abilities, and an explicit authorisation flow that throws an unauthorised exception when checks fail. Undefined abilities and invalid before callback return values are also surfaced through typed exceptions.

## Prerequisites

- **[PHP](https://www.php.net/)**: Version 8.3 or higher is required.
- **[Composer](https://getcomposer.org/)**: Dependency management tool for PHP.

## Installation

```bash
composer require andrewdyer/auth-gate
```

## Getting Started

### 1. Implement the actor

Any class that represents an authenticated actor must implement the `Authenticatable` interface. This is the object that will be evaluated against your defined abilities.

```php
use AndrewDyer\Gate\Contracts\Authenticatable;

class User implements Authenticatable
{
    public function __construct(
        public readonly int $id,
        public readonly bool $admin = false,
    ) {}

    public function isAdmin(): bool
    {
        return $this->admin;
    }
}
```

### 2. Create a Gate instance

Instantiate the `Gate` with the authenticated actor. This instance will be used to define and evaluate abilities.

```php
use AndrewDyer\Gate\Gate;

$actor = new User(id: 1);

$gate = new Gate($actor);
```

## Usage

The following examples demonstrate the available gate operations using the setup above.

### Defining Abilities

Abilities are registered via the `define` method, which accepts an ability name and a callback that returns a boolean.

```php
$gate->define('edit-post', function ($actor, $post) {
    return $actor->id === $post->authorId;
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
$gate->before(function ($actor, $ability) {
    if ($actor->isAdmin()) {
        return true;
    }
});
```

## License

Licensed under the [MIT license](https://opensource.org/licenses/MIT) and is free for private or commercial projects.
