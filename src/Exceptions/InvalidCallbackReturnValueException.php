<?php

declare(strict_types=1);

namespace AndrewDyer\Gate\Exceptions;

use LogicException;

/**
 * Exception thrown when a callback returns an invalid value.
 */
final class InvalidCallbackReturnValueException extends LogicException
{
    /**
     * Creates an instance for a before callback that returned a non-boolean non-null value.
     *
     * @param mixed $value The invalid value returned by the callback.
     *
     * @return self
     */
    public static function forBeforeCallback(mixed $value): self
    {
        return new self(
            sprintf(
                'Before callback must return bool or null, got %s.',
                get_debug_type($value)
            )
        );
    }
}
