<?php

declare(strict_types=1);

namespace AndrewDyer\Gate\Tests\Stubs;

/**
 * Stub representing a post resource used in gate authorization tests.
 */
final class Post
{
    /**
     * The unique identifier of the post.
     */
    private readonly int $id;

    /**
     * The identifier of the author who created the post.
     */
    private readonly int $authorId;

    /**
     * Creates a new Post stub instance.
     *
     * @param int $id       The unique identifier of the post.
     * @param int $authorId The identifier of the author who created the post.
     */
    public function __construct(
        int $id,
        int $authorId
    ) {
        $this->id = $id;
        $this->authorId = $authorId;
    }

    /**
     * Returns the identifier of the author who created the post.
     *
     * @return int The author identifier.
     */
    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    /**
     * Returns the unique identifier of the post.
     *
     * @return int The post identifier.
     */
    public function getId(): int
    {
        return $this->id;
    }
}
