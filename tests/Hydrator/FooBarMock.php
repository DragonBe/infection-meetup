<?php


namespace DragonBe\Test\Meetup\Hydrator;


class FooBarMock
{
    /**
     * @var FooMock
     */
    protected $foo;

    public function __construct(? FooMock $foo = null)
    {
        $this->foo = $foo;
    }

    public function getFoo(): FooMock
    {
        return $this->foo;
    }
}