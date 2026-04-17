<?php

namespace AndrewDyer\Gate\Tests\Stubs;

/**
 * Class Post.
 */
class Post
{
    /**
     * @var int
     */
    protected $authorId;
    /**
     * @var int
     */
    protected $id;

    /**
     * @return int
     */
    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $authorId
     *
     * @return $this
     */
    public function setAuthorId(int $authorId): self
    {
        $this->authorId = $authorId;

        return $this;
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
}
