<?php


namespace DragonBe\Test\Meetup\Hydrator;

use DragonBe\Meetup\Hydrator\JsonHydrator;
use PHPUnit\Framework\TestCase;

class JsonHydratorTest extends TestCase
{
    /**
     * Testing the hydrator can process JSON data
     *
     * @return void
     *
     * @covers \DragonBe\Meetup\Hydrator\JsonHydrator::hydrate()
     */
    public function testHydratorCanHydrateJsonIntoObject()
    {
        $foo = new FooMock();
        $fooJson = \json_encode(['foo' => 'bar']);

        $hydrator = new JsonHydrator();
        $fooObj = $hydrator->hydrate($fooJson, $foo);

        $this->assertInstanceOf(FooMock::class, $foo);
        $this->assertSame('bar', $fooObj->getFoo());
    }

    /**
     * Testing the hydrator can extract JSON data from an object
     *
     * @return void
     *
     * @covers \DragonBe\Meetup\Hydrator\JsonHydrator::extract()
     */
    public function testHydratorCanExtractJsonFromObject()
    {
        $data = ['foo' => 'bar'];
        $fooObj = new FooMock($data);
        $hydrator = new JsonHydrator();

        $this->assertSame(\json_encode($data), $hydrator->extract($fooObj));

    }
}
