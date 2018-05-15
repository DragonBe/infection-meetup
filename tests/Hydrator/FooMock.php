<?php


namespace DragonBe\Test\Meetup\Hydrator;


class FooMock
{
    /**
     * Property foo
     *
     * @var string
     */
    protected $foo;

    /**
     * FooMock constructor.
     *
     * @param array $data Key/value pairs
     */
    public function __construct(array $data = [])
    {
        if (array_key_exists('foo', $data)) {
            $this->foo = $data['foo'];
        }
    }

    /**
     * Retrieve the property value for foo
     *
     * @return string
     */
    public function getFoo(): string
    {
        return $this->foo;
    }

}