<?php


namespace DragonBe\Test\Meetup\Hydrator;

use DragonBe\Meetup\Hydrator\JsonCollectionHydrator;
use DragonBe\Meetup\Hydrator\JsonHydrator;
use PHPUnit\Framework\TestCase;

class JsonCollectionHydratorTest extends TestCase
{
    /**
     * Testing the hydrator can process JSON collection data
     *
     * @return void
     *
     * @covers \DragonBe\Meetup\Hydrator\JsonCollectionHydrator::hydrate()
     */
    public function testHydratorCanHydrateJsonCollectionIntoObjectCollection()
    {
        $data = [
            ['foo' => 'bar'],
            ['foo' => 'baz'],
            ['foo' => 'foobar'],
        ];
        $foo = new FooMock();
        $fooJson = \json_encode($data);

        $hydrator = new JsonCollectionHydrator();
        $fooIterator = $hydrator->hydrate($fooJson, $foo);

        $this->assertInstanceOf(\Iterator::class, $fooIterator);
        $this->assertCount(count($data), $fooIterator);

        $fooIterator->rewind();
        $fooObj = $fooIterator->current();
        $this->assertInstanceOf(FooMock::class, $fooObj);
        $this->assertSame($data[0]['foo'], $fooObj->getFoo());
    }

    /**
     * Testing the hydrator can extract JSON data from an object
     *
     * @return void
     *
     * @covers \DragonBe\Meetup\Hydrator\JsonCollectionHydrator::extract()
     */
    public function testHydratorCanExtractJsonCollectionFromObjectCollection()
    {
        $data = [
            ['foo' => 'bar'],
            ['foo' => 'baz'],
            ['foo' => 'foobar'],
        ];
        $fooCollection = new \splObjectStorage();
        foreach ($data as $entry) {
            $fooCollection->attach(new FooMock($entry));
        }

        $hydrator = new JsonCollectionHydrator();
        $jsonCollection = $hydrator->extract($fooCollection);
        $this->assertSame(\json_encode($data), $jsonCollection);
    }
}
