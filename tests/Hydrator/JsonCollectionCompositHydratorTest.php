<?php


namespace DragonBe\Test\Meetup\Hydrator;


use DragonBe\Meetup\Hydrator\JsonCollectionCompositHydrator;
use PHPUnit\Framework\TestCase;

/**
 * Class JsonCollectionCompositHydratorTest
 *
 * This class evaluates our composit hydration of JSON
 * composit collections.
 *
 * @package DragonBe\Test\Meetup\Hydrator
 */
class JsonCollectionCompositHydratorTest extends TestCase
{
    /**
     * Testing that a missing composit will throw an exception
     *
     * @return void
     *
     * @covers                   \DragonBe\Meetup\Hydrator\JsonCollectionCompositHydrator::hydrate()
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Composite hydrator expects a composit structure
     */
    public function testCompositJsonHydrationThrowsExceptionForMissingComposit()
    {
        $data = [
            ['foo' => ['foo' => 'bar']],
            ['foo' => ['foo' => 'baz']],
        ];
        $jsonData = \json_encode($data);
        $prototype = new FooBarMock();
        $hydrator = new JsonCollectionCompositHydrator();
        $foobarCollection = $hydrator->hydrate($jsonData, $prototype);
        $this->fail('Expecting exception to be thrown here');
    }

    /**
     * Testing composit hydration of JSON collections
     *
     * @return void
     *
     * @covers \DragonBe\Meetup\Hydrator\JsonCollectionCompositHydrator::hydrate()
     */
    public function testCanHydrateCompositeJson()
    {
        $data = [
            ['foo' => ['foo' => 'bar']],
            ['foo' => ['foo' => 'baz']],
        ];
        $jsonData = \json_encode($data);
        $prototype = new FooBarMock();
        $hydrator = new JsonCollectionCompositHydrator();
        $foobarCollection = $hydrator->hydrate($jsonData, $prototype, ['foo' => new FooMock()]);

        $foobarCollection->rewind();
        $foobar = $foobarCollection->current();
        $this->assertInstanceOf(FooMock::class, $foobar->getFoo());
    }

    /**
     * Test to see we can reverse a composite collection model
     *
     * @return void
     *
     * @covers \DragonBe\Meetup\Hydrator\JsonCollectionCompositHydrator::extract()
     */
    public function testExtractReturnsCompositJson()
    {
        $data = [
            ['foo' => ['foo' => 'bar']],
            ['foo' => ['foo' => 'baz']],
        ];
        $foobarCollection = new \SplObjectStorage();
        $hydrator = new JsonCollectionCompositHydrator();
        foreach ($data as $entity) {
            $foo = new FooMock($entity);
            $foobar = new FooBarMock($foo);
            $foobarCollection->attach($foobar);
        }
        $foobarJson = $hydrator->extract($foobarCollection);
        $this->assertSame(\json_encode($data), $foobarJson);

    }
}