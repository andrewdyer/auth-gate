<?php

declare(strict_types=1);

namespace AndrewDyer\Gate\Exceptions;

use LogicException;

/**
 * Exception thrown when an undefined ability is checked.
 */
final class UndefinedAbilityException extends LogicException
{
    /**
     * Creates an instance for a given ability name that has not been defined.
     *
     * @param string $ability The ability name that was not found.
     *
     * @return self
     */
    public static function forAbility(string $ability): self
    {
        return new self("Ability [{$ability}] is not defined.");
    }
}
