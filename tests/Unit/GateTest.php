<?php

namespace AndrewDyer\Gate\Tests\Unit;

use AndrewDyer\Gate\Gate;
use AndrewDyer\Gate\Tests\Stubs\Post;
use AndrewDyer\Gate\Tests\Stubs\User;
use AndrewDyer\Gate\UnauthorizedException;
use PHPUnit\Framework\TestCase;

/**
 * Class GateTest.
 */
final class GateTest extends TestCase
{
    /**
     * @var Gate
     */
    protected $gate;

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $user = new User();
        $user->setId(1);
        $user->setIsAdmin(true);

        $this->gate = new Gate($user);
    }

    /**
     * @test
     */
    public function actor_is_injected_into_closure_callbacks(): void
    {
        $this->gate->define('foo', function ($actor) {
            $this->assertEquals(1, $actor->getId());

            return true;
        });

        $this->assertTrue($this->gate->allows('foo'));
    }

    /**
     * @test
     */
    public function all_closure_callbacks_allow(): void
    {
        $this->gate->define('foo', function () {
            return true;
        });

        $this->gate->define('bar', function () {
            return true;
        });

        $this->assertTrue($this->gate->all(['bar', 'foo']));
    }

    /**
     * @test
     */
    public function authorize_throws_unauthorized_exception(): void
    {
        $this->expectException(UnauthorizedException::class);

        $this->gate->define('foo', function () {
            return false;
        });

        $this->gate->authorize(['foo']);
    }

    public function before_callbacks_can_override_result_if_necessary(): void
    {
        $this->gate->define('foo', function () {
            return true;
        });

        $this->gate->before(function ($actor, $ability) {
            $this->assertEquals('foo', $ability);

            return false;
        });

        $this->assertFalse($this->gate->allows('foo'));
    }

    /**
     * @test
     */
    public function before_callbacks_dont_interrupt_gate_check_if_no_value_is_returned(): void
    {
        $this->gate->define('foo', function () {
            return false;
        });

        $this->gate->before(function ($actor, $ability) {
            $this->assertEquals('foo', $ability);
        });

        $this->assertFalse($this->gate->allows('foo'));
    }

    /**
     * @test
     */
    public function can_pass_a_single_argument_when_checking_abilities(): void
    {
        $post = new Post();
        $post->setId(1);
        $post->setAuthorId(1);

        $this->gate->define('foo', function ($actor, $x) use ($post) {
            $this->assertEquals($post, $x);

            return true;
        });

        $this->assertTrue($this->gate->allows('foo', $post));
    }

    /**
     * @test
     */
    public function closure_callback_is_denied(): void
    {
        $this->gate->define('foo', function () {
            return true;
        });

        $this->assertFalse($this->gate->denies('foo'));
    }

    /**
     * @test
     */
    public function closures_can_be_defined(): void
    {
        $this->gate->define('foo', function () {
            return true;
        });

        $this->assertTrue($this->gate->allows('foo'));
    }

    /**
     * @test
     */
    public function multiple_arguments_can_be_passed_when_checking_abilities()
    {
        $post = new Post();
        $post->setId(1);
        $post->setAuthorId(1);

        $secondPost = new Post();
        $secondPost->setId(2);
        $secondPost->setAuthorId(1);

        $this->gate->define('foo', function ($actor, $x, $y) use ($post, $secondPost) {
            $this->assertEquals($post, $x);
            $this->assertEquals($secondPost, $y);

            return true;
        });

        $this->assertTrue($this->gate->allows('foo', $post, $secondPost));
    }

    /**
     * @test
     */
    public function multiple_closures_can_be_defined(): void
    {
        $this->gate->define('foo', function () {
            return true;
        });

        $this->gate->define('bar', function () {
            return false;
        });

        $this->assertTrue($this->gate->any(['bar', 'foo']));
    }
}
