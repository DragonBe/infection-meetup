<?php


namespace DragonBe\Test\Meetup\Factory;


use DragonBe\Meetup\Consumer;
use DragonBe\Meetup\Factory\ConsumerFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class ConsumerFactoryTest
 *
 * This test case aims at verifying that ConsumerFactory
 * produces a correct Consumer instance that can be used
 * for retrieving the information from Meetup.com.
 *
 * @package DragonBe\Test\Meetup\Factory
 */
class ConsumerFactoryTest extends TestCase
{
    /**
     * Testing that a Consumer object can be created when invoked
     *
     * @return void
     *
     * @covers \DragonBe\Meetup\Factory\ConsumerFactory::__invoke()
     */
    public function testConsumerFactoryCanInvokeConsumerObject()
    {
        $consumerFactory = new ConsumerFactory();
        $consumer = $consumerFactory();

        $this->assertInstanceOf(Consumer::class, $consumer);
    }

    /**
     * Testing that we can invoke a Consumer object through the
     * factory with a custom URI (used for testing)
     *
     * @return void
     *
     * @covers \DragonBe\Meetup\Factory\ConsumerFactory::__invoke
     */
    public function testConsumerFactoryCanInvokeConsumerObjectWithCustomUri()
    {
        $consumerFactory = new ConsumerFactory();
        $consumer = $consumerFactory('http://www.example.com');

        $this->assertInstanceOf(Consumer::class, $consumer);
    }
}