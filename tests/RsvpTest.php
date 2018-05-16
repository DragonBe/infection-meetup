<?php


namespace DragonBe\Test\Meetup;


use DragonBe\Meetup\Event;
use DragonBe\Meetup\Group;
use DragonBe\Meetup\Hydrator\JsonCollectionCompositHydrator;
use DragonBe\Meetup\Member;
use DragonBe\Meetup\Rsvp;
use PHPUnit\Framework\TestCase;

/**
 * Class RsvpTest
 *
 * This test evaluates the correct usage of Meetup.com's
 * RSVP data set so we can build a composite structure to
 * create a complete overview of an RSVP.
 *
 * @package DragonBe\Test\Meetup
 */
class RsvpTest extends TestCase
{
    /**
     * Data provider for a composite data structure
     *
     * @return array
     */
    public function rsvpDataProvider(): array
    {
        return [
            [
                'groupId' => 18381650,
                'groupName' => 'PHP Leuven - Web Innovation Group',
                'eventId' => 'sctxfnyxjbkb',
                'eventName' => 'Infection',
                'memberId' => 6536041,
                'memberName' => 'Michelangelo van D.',
            ],
        ];
    }

    /**
     * Testing if we can hydrate an RSVP model
     *
     * @param int    $groupId    ID of Meetup.com group
     * @param string $groupName  Name of Meetup.com group
     * @param string $eventId    ID of event for group
     * @param string $eventName  Name of event for group
     * @param int    $memberId   ID of member
     * @param string $memberName Name of member
     *
     * @return void
     *
     * @covers \DragonBe\Meetup\Rsvp::__construct()
     * @covers \DragonBe\Meetup\Rsvp::getGroup()
     * @covers \DragonBe\Meetup\Rsvp::getEvent()
     * @covers \DragonBe\Meetup\Rsvp::getMember()
     *
     * @dataProvider rsvpDataProvider
     */
    public function testCanCreateAnRsvpModel(
        int $groupId,
        string $groupName,
        string $eventId,
        string $eventName,
        int $memberId,
        string $memberName
    ) {
        $data = [
            [
                'group' => [
                    'id' => $groupId,
                    'name' => $groupName,
                ],
                'event' => [
                    'id' => $eventId,
                    'name' => $eventName,
                ],
                'member' => [
                    'id' => $memberId,
                    'name' => $memberName,
                ],
            ],
        ];
        $dataJson = \json_encode($data);
        $rsvpPrototype = new Rsvp();
        $compositHydrator = new JsonCollectionCompositHydrator();
        $rsvpCollection = $compositHydrator->hydrate(
            $dataJson,
            $rsvpPrototype,
            [
                'group' => new Group(),
                'event' => new Event(),
                'member' => new Member(),
            ]
        );
        $rsvpCollection->rewind();
        $rsvpEntity = $rsvpCollection->current();

        $this->assertInstanceOf(Group::class, $rsvpEntity->getGroup());
        $this->assertInstanceOf(Event::class, $rsvpEntity->getEvent());
        $this->assertInstanceOf(Member::class, $rsvpEntity->getMember());
    }
}