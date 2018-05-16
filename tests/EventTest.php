<?php


namespace DragonBe\Test\Meetup;


use DragonBe\Meetup\Event;
use DragonBe\Meetup\Hydrator\JsonCollectionHydrator;
use DragonBe\Meetup\Hydrator\JsonHydrator;
use PHPUnit\Framework\TestCase;

/**
 * Class EventTest
 *
 * This class tests the behaviour of an Event object
 *
 * @package DragonBe\Test\Meetup
 */
class EventTest extends TestCase
{
    /**
     * An event data provider
     *
     * @return array
     */
    public function eventProvider(): array
    {
        return [
            ['sctxfnyxjbkb', 4],
        ];
    }

    /**
     * Testing that we can retrieve upcoming events from group
     *
     * @covers       \DragonBe\Meetup\Event::__construct()
     * @covers       \DragonBe\Meetup\Event::getId()
     * @covers       \DragonBe\Meetup\Event::getRsvp()
     * @dataProvider eventProvider
     */
    public function testCanRetrieveUpcomingEventListFromGroup($id, $rsvp)
    {
        $hydrator = new JsonCollectionHydrator();
        $eventObj = new Event();
        $eventsJson = file_get_contents(__DIR__ . '/_files/meetup_group_upcoming.json');
        $eventCollection = $hydrator->hydrate($eventsJson, $eventObj);

        $this->assertCount(12, $eventCollection);

        $eventCollection->rewind();

        $event = $eventCollection->current();
        $this->assertSame($id, $event->getId());
        $this->assertSame($rsvp, $event->getRsvp());
    }

    /**
     * Testing we can retrieve a single event
     *
     * @param string $id   The ID of the event
     * @param int    $rsvp The number of members participating in the event
     *
     * @covers       \DragonBe\Meetup\Event::__construct()
     * @covers       \DragonBe\Meetup\Event::getId()
     * @covers       \DragonBe\Meetup\Event::getRsvp()
     * @dataProvider eventProvider
     */
    public function testCanRetrieveSingleEventFromGroup(string $id, int $rsvp)
    {
        $hydrator = new JsonHydrator();
        $eventObj = new Event();
        $eventsJson = file_get_contents(__DIR__ . '/_files/meetup_group_event.json');
        $event = $hydrator->hydrate($eventsJson, $eventObj);

        $this->assertSame($id, $event->getId());
        $this->assertSame($rsvp, $event->getRsvp());
    }
}