<?php

namespace AndrewDyer\Gate\Tests\Stubs;

use AndrewDyer\Gate\Authenticatable;

/**
 * Class User.
 */
class User implements Authenticatable
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var bool
     */
    protected $isAdmin;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function getIsAdmin(): bool
    {
        return $this->isAdmin;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param bool $isAdmin
     *
     * @return $this
     */
    public function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }
}
