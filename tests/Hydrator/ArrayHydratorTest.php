<?php


namespace DragonBe\Test\Meetup\Hydrator;

use DragonBe\Meetup\Hydrator\ArrayHydrator;
use PHPUnit\Framework\TestCase;

class ArrayHydratorTest extends TestCase
{
    /**
     * Testing the hydrator can process data arrays
     *
     * @return void
     *
     * @covers \DragonBe\Meetup\Hydrator\ArrayHydrator::hydrate()
     */
    public function testHydratorCanHydrateArrayIntoObject()
    {
        $foo = new FooMock();
        $fooArray = ['foo' => 'bar'];

        $hydrator = new ArrayHydrator();
        $fooObj = $hydrator->hydrate($fooArray, $foo);

        $this->assertInstanceOf(FooMock::class, $foo);
        $this->assertSame('bar', $fooObj->getFoo());
    }

    /**
     * Testing the hydrator can extract data from an object
     *
     * @return void
     *
     * @covers \DragonBe\Meetup\Hydrator\ArrayHydrator::extract()
     */
    public function testHydratorCanExtractArrayFromObject()
    {
        $data = ['foo' => 'bar'];
        $fooObj = new FooMock($data);
        $hydrator = new ArrayHydrator();

        $this->assertSame($data, $hydrator->extract($fooObj));

    }
}
