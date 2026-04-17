<?php

declare(strict_types=1);

namespace AndrewDyer\Gate;

use AndrewDyer\Gate\Contracts\Authenticatable;
use AndrewDyer\Gate\Exceptions\InvalidCallbackReturnValueException;
use AndrewDyer\Gate\Exceptions\UnauthorizedException;
use AndrewDyer\Gate\Exceptions\UndefinedAbilityException;

/**
 * Manages ability definitions and authorisation checks for an authenticated actor.
 */
final class Gate
{
    /**
     * Registered ability callbacks indexed by ability name.
     */
    private array $abilities = [];

    /**
     * Callbacks to run before ability checks.
     */
    private array $beforeCallbacks = [];

    /**
     * Creates a new Gate instance for the given actor.
     *
     * @param Authenticatable $actor The authenticated actor performing actions.
     */
    public function __construct(private readonly Authenticatable $actor)
    {
    }

    /**
     * Determines whether the actor is allowed all of the given abilities.
     *
     * @param array $abilities The ability names to check.
     * @param mixed ...$args   Additional arguments passed to ability callbacks.
     *
     * @return bool True if all abilities are allowed, false otherwise.
     *
     * @throws UndefinedAbilityException           When any of the given abilities has not been defined.
     * @throws InvalidCallbackReturnValueException When a before callback returns a non-boolean non-null value.
     */
    public function all(array $abilities, mixed ...$args): bool
    {
        return $this->check($abilities, ...$args);
    }

    /**
     * Determines whether the actor is allowed the given ability.
     *
     * @param string $ability The ability name to check.
     * @param mixed  ...$args Additional arguments passed to the ability callback.
     *
     * @return bool True if the ability is allowed, false otherwise.
     *
     * @throws UndefinedAbilityException           When the given ability has not been defined.
     * @throws InvalidCallbackReturnValueException When a before callback returns a non-boolean non-null value.
     */
    public function allows(string $ability, mixed ...$args): bool
    {
        return $this->check([$ability], ...$args);
    }

    /**
     * Determines whether the actor is allowed at least one of the given abilities.
     *
     * @param array $abilities The ability names to check.
     * @param mixed ...$args   Additional arguments passed to ability callbacks.
     *
     * @return bool True if any ability is allowed, false otherwise.
     *
     * @throws UndefinedAbilityException           When any of the given abilities has not been defined.
     * @throws InvalidCallbackReturnValueException When a before callback returns a non-boolean non-null value.
     */
    public function any(array $abilities, mixed ...$args): bool
    {
        foreach ($abilities as $ability) {
            if ($this->check([$ability], ...$args)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Authorises the actor for all of the given abilities, or throws an exception.
     *
     * @param array $abilities The ability names to check.
     * @param mixed ...$args   Additional arguments passed to ability callbacks.
     *
     * @return void
     *
     * @throws UnauthorizedException               When any of the given abilities is not allowed.
     * @throws UndefinedAbilityException           When any of the given abilities has not been defined.
     * @throws InvalidCallbackReturnValueException When a before callback returns a non-boolean non-null value.
     */
    public function authorize(array $abilities, mixed ...$args): void
    {
        if (!$this->check($abilities, ...$args)) {
            throw UnauthorizedException::forAction();
        }
    }

    /**
     * Registers a callback to run before all ability checks.
     *
     * @param callable $beforeCallback The callback to register.
     *
     * @return self
     */
    public function before(callable $beforeCallback): self
    {
        $this->beforeCallbacks[] = $beforeCallback;

        return $this;
    }

    /**
     * Registers a callback for the given ability.
     *
     * @param string   $ability         The ability name to register.
     * @param callable $abilityCallback The callback that determines whether the ability is allowed.
     *
     * @return self
     */
    public function define(string $ability, callable $abilityCallback): self
    {
        $this->abilities[$ability] = $abilityCallback;

        return $this;
    }

    /**
     * Determines whether the actor is denied the given ability.
     *
     * @param string $ability The ability name to check.
     * @param mixed  ...$args Additional arguments passed to the ability callback.
     *
     * @return bool True if the ability is denied, false otherwise.
     *
     * @throws UndefinedAbilityException           When the given ability has not been defined.
     * @throws InvalidCallbackReturnValueException When a before callback returns a non-boolean non-null value.
     */
    public function denies(string $ability, mixed ...$args): bool
    {
        return !$this->check([$ability], ...$args);
    }

    /**
     * Determines whether all of the given abilities pass inspection.
     *
     * @param array $abilities The ability names to check.
     * @param mixed ...$args   Arguments passed to ability callbacks.
     *
     * @return bool True if all abilities pass, false otherwise.
     *
     * @throws UndefinedAbilityException           When any of the given abilities has not been defined.
     * @throws InvalidCallbackReturnValueException When a before callback returns a non-boolean non-null value.
     *
     * @internal
     */
    private function check(array $abilities, mixed ...$args): bool
    {
        foreach ($abilities as $ability) {
            if (!$this->inspect($ability, ...$args)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Processes before callbacks and evaluates the ability callback for the given ability.
     *
     * @param string $ability The ability name to inspect.
     * @param mixed  ...$args Arguments passed to the ability callback.
     *
     * @return bool True if the ability is allowed, false otherwise.
     *
     * @throws UndefinedAbilityException           When the given ability has not been defined.
     * @throws InvalidCallbackReturnValueException When a before callback returns a non-boolean non-null value.
     *
     * @internal
     */
    private function inspect(string $ability, mixed ...$args): bool
    {
        foreach ($this->beforeCallbacks as $beforeCallback) {
            $result = $beforeCallback($this->actor, $ability);

            if ($result === null) {
                continue;
            }

            if (!is_bool($result)) {
                throw InvalidCallbackReturnValueException::forBeforeCallback($result);
            }

            return $result;
        }

        if (!isset($this->abilities[$ability])) {
            throw UndefinedAbilityException::forAbility($ability);
        }

        return $this->abilities[$ability]($this->actor, ...$args);
    }
}
