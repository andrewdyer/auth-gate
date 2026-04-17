<?php

namespace AndrewDyer\Gate;

/**
 * Class Gate.
 */
final class Gate
{
    /**
     * All of the defined abilities.
     *
     * @var array
     */
    protected $abilities = [];

    /**
     * The user performing the action.
     *
     * @var Authenticatable
     */
    protected $actor;

    /**
     * All of the registered before callbacks.
     *
     * @var array
     */
    protected $beforeCallbacks = [];

    /**
     * Gate constructor.
     *
     * @param Authenticatable $actor the user performing the action
     */
    public function __construct(Authenticatable $actor)
    {
        $this->actor = $actor;
    }

    /**
     * Checks if all the given abilities should be granted for the actor.
     *
     * @param array $abilities
     * @param mixed ...$args
     *
     * @return bool
     */
    public function all(array $abilities, ...$args): bool
    {
        return $this->check($abilities, $args);
    }

    /**
     * Checks if the given ability should be granted for the actor.
     *
     * @param string $ability
     * @param mixed  ...$args
     *
     * @return bool
     */
    public function allows(string $ability, ...$args): bool
    {
        return $this->check([$ability], $args);
    }

    /**
     * Checks if any of the given abilities should be granted for the actor.
     *
     * @param array $abilities
     * @param mixed ...$args
     *
     * @return bool
     */
    public function any(array $abilities, ...$args): bool
    {
        foreach ($abilities as $ability) {
            if ($this->check([$ability], $args)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if all the given abilities should be granted for the actor and throws an unauthorized exception.
     *
     * @param array $abilities
     * @param mixed ...$args
     *
     * @throws UnauthorizedException
     */
    public function authorize(array $abilities, ...$args): void
    {
        if (!$this->check($abilities, $args)) {
            throw new UnauthorizedException('This action is unauthorized.');
        }
    }

    /**
     * Register a callback to run before all checks.
     *
     * @param callable $beforeCallback
     *
     * @return $this
     */
    public function before(callable $beforeCallback): self
    {
        $this->beforeCallbacks[] = $beforeCallback;

        return $this;
    }

    /**
     * Register a new ability.
     *
     * @param string   $ability
     * @param callable $abilityCallback
     *
     * @return $this
     */
    public function define(string $ability, callable $abilityCallback): self
    {
        $this->abilities[$ability] = $abilityCallback;

        return $this;
    }

    /**
     * Checks if the given ability should be denied for the actor.
     *
     * @param string $ability
     * @param mixed  ...$args
     *
     * @return bool
     */
    public function denies(string $ability, ...$args): bool
    {
        return !$this->check([$ability], $args);
    }

    /**
     * Checks if the given abilities should be granted for the actor.
     *
     * @param array $abilities
     * @param array $args
     *
     * @return bool
     */
    protected function check(array $abilities, array $args = []): bool
    {
        foreach ($abilities as $ability) {
            if (!$this->inspect($ability, $args)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $ability
     * @param array  $args
     *
     * @return bool
     */
    protected function inspect(string $ability, array $args): bool
    {
        foreach ($this->beforeCallbacks as $beforeCallback) {
            if ($beforeCallback($this->actor, $ability)) {
                return true;
            }
        }

        return $this->abilities[$ability]($this->actor, ...$args);
    }
}
