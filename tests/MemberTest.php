<?php


namespace DragonBe\Test\Meetup;

use DragonBe\Meetup\Hydrator\JsonCollectionHydrator;
use DragonBe\Meetup\Hydrator\JsonHydrator;
use DragonBe\Meetup\Member;
use PHPUnit\Framework\TestCase;

/**
 * Class MemberTest
 *
 * This class tests the behaviour of an Member object
 *
 * @package DragonBe\Test\Meetup
 */
class MemberTest extends TestCase
{
    /**
     * Data provider for member data
     *
     * @return array
     */
    public function memberDataProvider(): array
    {
        return [
            [140063382, 'Bart R.'],
        ];
    }

    /**
     * Testing that we get default values when member data is
     * not available
     *
     * @return void
     *
     * @covers \DragonBe\Meetup\Member::__construct()
     * @covers \DragonBe\Meetup\Member::getId()
     * @covers \DragonBe\Meetup\Member::getName()
     */
    public function testMemberReturnsDefaultValuesWhenDataIsMissing()
    {
        $testData = \json_encode(['foo' => 'bar']);
        $memberObj = new Member();
        $hydrator = new JsonHydrator();
        $memberEntity = $hydrator->hydrate($testData, $memberObj);
        $this->assertSame(0, $memberEntity->getId());
        $this->assertSame('', $memberEntity->getName());
    }

    /**
     * Test to see we can hydrate json data into a member object
     *
     * @param int    $memberId   The ID of a Meetup.com member
     * @param string $memberName The name of a Meetup.com member
     *
     * @covers       \DragonBe\Meetup\Member::__construct()
     * @covers       \DragonBe\Meetup\Member::getId()
     * @covers       \DragonBe\Meetup\Member::getName()
     * @dataProvider memberDataProvider
     */
    public function testMemberCanBeHydratedFromMemberJson(int $memberId, string $memberName)
    {
        $testData = \json_encode(['id' => $memberId, 'name' => $memberName]);
        $memberObj = new Member();
        $hydrator = new JsonHydrator();
        $memberEntity = $hydrator->hydrate($testData, $memberObj);

        $this->assertSame($memberId, $memberEntity->getId());
        $this->assertSame($memberName, $memberEntity->getName());
    }

    /**
     * Test to retrieve member objects from the Meetup.com group
     * member list.
     *
     * @param int    $memberId   The ID of the Meetup.com member
     * @param string $memberName The name of the Meetup.com member
     *
     * @covers       \DragonBe\Meetup\Member::__construct()
     * @covers       \DragonBe\Meetup\Member::getId()
     * @covers       \DragonBe\Meetup\Member::getName()
     * @dataProvider memberDataProvider
     */
    public function testCanRetrieveMemberListFromGroup(int $memberId, string $memberName)
    {
        $testData = file_get_contents(__DIR__ . '/_files/meetup_group_members.json');
        $memberObj = new Member();
        $collectionHydrator = new JsonCollectionHydrator();

        $memberCollection = $collectionHydrator->hydrate($testData, $memberObj);

        $this->assertInstanceOf(\Iterator::class, $memberCollection);
        $this->assertCount(200, $memberCollection);

        $memberCollection->rewind();
        $memberEntity = $memberCollection->current();
        $this->assertInstanceOf(Member::class, $memberEntity);
        $this->assertSame($memberId, $memberEntity->getId());
        $this->assertSame($memberName, $memberEntity->getName());
    }
}