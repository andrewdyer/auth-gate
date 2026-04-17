<?php

declare(strict_types=1);

namespace AndrewDyer\Gate;

use LogicException;

/**
 * Exception thrown when an undefined ability is checked.
 */
final class UndefinedAbilityException extends LogicException
{
}
