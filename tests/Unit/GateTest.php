<?php

declare(strict_types=1);

namespace AndrewDyer\Gate\Tests\Unit;

use AndrewDyer\Gate\Gate;
use AndrewDyer\Gate\Tests\Stubs\Post;
use AndrewDyer\Gate\Tests\Stubs\User;
use AndrewDyer\Gate\UnauthorizedException;
use AndrewDyer\Gate\UndefinedAbilityException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Gate.
 */
final class GateTest extends TestCase
{
    /**
     * The Gate instance under test.
     */
    private Gate $gate;

    /**
     * Sets up the Gate instance with a default authenticated user.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $user = new User(1, true);

        $this->gate = new Gate($user);
    }

    /**
     * Asserts that the actor is injected into closure callbacks.
     */
    public function testActorIsInjectedIntoClosureCallbacks(): void
    {
        $this->gate->define('foo', function($actor) {
            self::assertEquals(1, $actor->getId());

            return true;
        });

        self::assertTrue($this->gate->allows('foo'));
    }

    /**
     * Asserts that all closure callbacks allow access when all abilities are granted.
     */
    public function testAllClosureCallbacksAllow(): void
    {
        $this->gate->define('foo', function() {
            return true;
        });

        $this->gate->define('bar', function() {
            return true;
        });

        self::assertTrue($this->gate->all(['bar', 'foo']));
    }

    /**
     * Asserts that any() returns true when at least one ability is allowed.
     */
    public function testAnyReturnsTrueWhenAtLeastOneAbilityIsAllowed(): void
    {
        $this->gate->define('foo', function() {
            return false;
        });

        $this->gate->define('bar', function() {
            return true;
        });

        self::assertTrue($this->gate->any(['foo', 'bar']));
    }

    /**
     * Asserts that any() returns false when no abilities are allowed.
     */
    public function testAnyReturnsFalseWhenNoAbilitiesAreAllowed(): void
    {
        $this->gate->define('foo', function() {
            return false;
        });

        $this->gate->define('bar', function() {
            return false;
        });

        self::assertFalse($this->gate->any(['foo', 'bar']));
    }

    /**
     * Asserts that authorize throws an UnauthorizedException when the ability is denied.
     */
    public function testAuthorizeThrowsUnauthorizedException(): void
    {
        $this->expectException(UnauthorizedException::class);

        $this->gate->define('foo', function() {
            return false;
        });

        $this->gate->authorize(['foo']);
    }

    /**
     * Asserts that before callbacks can override the result of an ability check.
     */
    public function testBeforeCallbacksCanOverrideResultIfNecessary(): void
    {
        $this->gate->define('foo', function() {
            return true;
        });

        $this->gate->before(function($actor, $ability) {
            self::assertEquals('foo', $ability);

            return false;
        });

        self::assertFalse($this->gate->allows('foo'));
    }

    /**
     * Asserts that before callbacks do not interrupt the gate check when no value is returned.
     */
    public function testBeforeCallbacksDontInterruptGateCheckIfNoValueIsReturned(): void
    {
        $this->gate->define('foo', function() {
            return false;
        });

        $this->gate->before(function($actor, $ability) {
            self::assertEquals('foo', $ability);
        });

        self::assertFalse($this->gate->allows('foo'));
    }

    /**
     * Asserts that a single argument can be passed when checking abilities.
     */
    public function testCanPassASingleArgumentWhenCheckingAbilities(): void
    {
        $post = new Post(1, 1);

        $this->gate->define('foo', function($actor, $x) use ($post) {
            self::assertEquals($post, $x);

            return true;
        });

        self::assertTrue($this->gate->allows('foo', $post));
    }

    /**
     * Asserts that multiple arguments can be passed when checking abilities.
     */
    public function testCanPassMultipleArgumentsWhenCheckingAbilities(): void
    {
        $post = new Post(1, 1);
        $extra = 'context';

        $this->gate->define('foo', function($actor, $x, $y) use ($post, $extra) {
            self::assertEquals($post, $x);
            self::assertEquals($extra, $y);

            return true;
        });

        self::assertTrue($this->gate->allows('foo', $post, $extra));
    }

    /**
     * Asserts that denies returns false when the ability is allowed.
     */
    public function testClosureCallbackIsDenied(): void
    {
        $this->gate->define('foo', function() {
            return true;
        });

        self::assertFalse($this->gate->denies('foo'));
    }

    /**
     * Asserts that checking an undefined ability throws an UndefinedAbilityException.
     */
    public function testCheckingUndefinedAbilityThrowsException(): void
    {
        $this->expectException(UndefinedAbilityException::class);
        $this->expectExceptionMessage('Ability [foo] is not defined.');

        $this->gate->allows('foo');
    }

    /**
     * Asserts that closure callbacks can be defined and evaluated as abilities.
     */
    public function testClosuresCanBeDefined(): void
    {
        $this->gate->define('foo', function() {
            return true;
        });

        self::assertTrue($this->gate->allows('foo'));
    }
}
