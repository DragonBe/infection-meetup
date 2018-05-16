<?php


namespace DragonBe\Test\Meetup;


use DragonBe\Meetup\Hydrator\JsonHydrator;
use DragonBe\Meetup\Group;
use PHPUnit\Framework\TestCase;

/**
 * Class GroupTest
 *
 * This class tests the behaviour of a Group object
 *
 * @package DragonBe\Test\Meetup
 */
class GroupTest extends TestCase
{
    /**
     * Testing that an exception is thrown for bad group details
     *
     * @return void
     *
     * @covers \DragonBe\Meetup\Group::__construct()
     * @covers \DragonBe\Meetup\Group::getId()
     * @covers \DragonBe\Meetup\Group::getName()
     */
    public function testBadGroupDetailsReturnsNullValues()
    {
        $hydrator = new JsonHydrator();
        $groupObj = new Group();
        $groupJson = '{"foo": "bar"}';
        $group = $hydrator->hydrate($groupJson, $groupObj);

        $this->assertSame(0, $group->getId());
        $this->assertSame('', $group->getName());
    }

    /**
     * Testing that we can retrieve group details
     *
     * @return void
     *
     * @covers \DragonBe\Meetup\Group::__construct()
     * @covers \DragonBe\Meetup\Group::getId()
     * @covers \DragonBe\Meetup\Group::getName()
     */
    public function testRetrieveGroupDetails()
    {
        $hydrator = new JsonHydrator();
        $groupObj = new Group();
        $groupJson = file_get_contents(__DIR__ . '/_files/meetup_group.json');
        $group = $hydrator->hydrate($groupJson, $groupObj);

        $id = $group->getId();
        $this->assertSame(18381650, $group->getId());
        $name = $group->getName();
        $this->assertSame('PHP Leuven - Web Innovation Group', $name);
    }
}