<?php

declare(strict_types=1);

namespace AndrewDyer\Gate\Exceptions;

use RuntimeException;

/**
 * Exception thrown when an actor is not authorised to perform an action.
 */
final class UnauthorizedException extends RuntimeException
{
    /**
     * Creates an instance for an unauthorised action.
     *
     * @return self
     */
    public static function forAction(): self
    {
        return new self('This action is unauthorized.');
    }
}
