<?php

declare(strict_types=1);

namespace AndrewDyer\Gate\Tests\Stubs;

use AndrewDyer\Gate\Authenticatable;

/**
 * Stub implementation of Authenticatable representing a user actor.
 */
final class User implements Authenticatable
{
    /**
     * The unique identifier of the user.
     */
    private readonly int $id;

    /**
     * Indicates whether the user has administrator privileges.
     */
    private readonly bool $isAdmin;

    /**
     * Creates a new User stub instance.
     *
     * @param int  $id      The unique identifier of the user.
     * @param bool $isAdmin Indicates whether the user has administrator privileges.
     */
    public function __construct(
        int $id,
        bool $isAdmin
    ) {
        $this->id = $id;
        $this->isAdmin = $isAdmin;
    }

    /**
     * Returns the unique identifier of the user.
     *
     * @return int The user identifier.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Returns whether the user has administrator privileges.
     *
     * @return bool True if the user is an administrator, false otherwise.
     */
    public function getIsAdmin(): bool
    {
        return $this->isAdmin;
    }
}
