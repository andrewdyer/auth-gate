# Gate

[![Latest Stable Version](https://poser.pugx.org/andrewdyer/gate/v/stable)](https://packagist.org/packages/andrewdyer/gate)
[![Total Downloads](https://poser.pugx.org/andrewdyer/gate/downloads)](https://packagist.org/packages/andrewdyer/gate)
[![Daily Downloads](https://poser.pugx.org/andrewdyer/gate/d/daily)](https://packagist.org/packages/andrewdyer/gate)
[![Monthly Downloads](https://poser.pugx.org/andrewdyer/gate/d/monthly)](https://packagist.org/packages/andrewdyer/gate)
[![Latest Unstable Version](https://poser.pugx.org/andrewdyer/gate/v/unstable)](https://packagist.org/packages/andrewdyer/gate)
[![License](https://poser.pugx.org/andrewdyer/gate/license)](https://packagist.org/packages/andrewdyer/gate)
[![composer.lock](https://poser.pugx.org/andrewdyer/gate/composerlock)](https://packagist.org/packages/andrewdyer/gate)

Check if a user is authorized to perform a given action.

## License
Licensed under MIT. Totally free for private or commercial projects.

## Installation
```text
composer require andrewdyer/gate
```

## Getting Started

To get started with the Gate library, you need to create an instance of the `Gate` class and pass an `Authenticatable` user to it.

```php
use AndrewDyer\Gate\Gate;
use AndrewDyer\Gate\Authenticatable;

class User implements Authenticatable {
    // User implementation
}

$user = new User();
$gate = new Gate($user);
```

## Usage

### Defining Abilities

You can define abilities using the `define` method. The first argument is the name of the ability, and the second argument is a callback that determines if the user has the ability.

```php
$gate->define('edit-post', function ($user, $post) {
    return $user->id === $post->user_id;
});
```

### Checking Abilities

You can check abilities using the `allows` and `denies` methods.

```php
if ($gate->allows('edit-post', $post)) {
    // The user can edit the post
}

if ($gate->denies('edit-post', $post)) {
    // The user cannot edit the post
}
```

### Authorizing Actions

You can authorize actions using the `authorize` method. This method will throw an `UnauthorizedException` if the user does not have the required abilities.

```php
try {
    $gate->authorize(['edit-post'], $post);
    // The user is authorized to edit the post
} catch (UnauthorizedException $e) {
    // The user is not authorized to edit the post
}
```

### Registering Before Callbacks

You can register a callback to run before all checks using the `before` method.

```php
$gate->before(function ($user, $ability) {
    if ($user->isAdmin()) {
        return true;
    }
});
```
