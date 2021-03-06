<?php


namespace DragonBe\Test\Meetup;


use DragonBe\Meetup\Consumer;
use DragonBe\Meetup\Event;
use DragonBe\Meetup\Group;
use DragonBe\Meetup\Hydrator\JsonCollectionCompositHydrator;
use DragonBe\Meetup\Hydrator\JsonCollectionHydrator;
use DragonBe\Meetup\Hydrator\JsonHydrator;
use DragonBe\Meetup\Member;
use DragonBe\Meetup\Rsvp;
use Faker\Factory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * Class ConsumerTest
 *
 * This test case evaluates the main Consumer class that sets up
 * the connection with Meetup.com API and interacts with its
 * endpoints.
 *
 * @package DragonBe\Test\Meetup
 * @see     https://secure.meetup.com/meetup_api
 */
class ConsumerTest extends TestCase
{
    /**
     * A generic key used for testing
     *
     * @var string
     */
    protected $apiKey;

    /**
     * An existing test group on Meetup.com
     *
     * @var string
     */
    protected $testGroup;

    /**
     * An existing test event ID
     *
     * @var string
     */
    protected $testEventId;

    /**
     * Setting up the Consumer class before each test
     *
     * @return void
     */
    protected function setUp()
    {
        $this->apiKey = '336659596827465517a104415731e4c';
        $this->testGroup = 'PHP-Leuven-Web-Innovation-Group';
        $this->testEventId = 'sctxfnyxjbkb';
    }

    /**
     * Destructor for the Consumer class after each test
     *
     * @return void
     */
    protected function tearDown()
    {
        $this->consumer = null;
        $this->apiKey = null;
        $this->testEventId = null;
    }

    /**
     * Creates a Consumer object with given HTTP
     * client
     *
     * @param Client $client A GuzzleHttp\Client
     *
     * @return Consumer
     */
    private function getConsumer(Client $client): Consumer
    {
        $hydrator = new JsonHydrator();
        $collectionHydrator = new JsonCollectionHydrator();
        $compositeHydrator = new JsonCollectionCompositHydrator();
        $group = new Group();
        $event = new Event();
        $member = new Member();
        $rsvp = new Rsvp();
        $consumer = new Consumer(
            $hydrator,
            $collectionHydrator,
            $compositeHydrator,
            $group,
            $event,
            $member,
            $rsvp,
            $client,
            $this->apiKey
        );
        return $consumer;
    }

    /**
     * A data provider for bad URI's
     *
     * @return array
     */
    public function badUriProvider(): array
    {
        return [
            ['http://api.thisdomaindoesnotexists.tld']
        ];
    }

    /**
     * Data provider for event ID's
     *
     * @return array
     */
    public function eventIdProvider(): array
    {
        return [
            ['PHP-Leuven-Web-Innovation-Group', 'sctxfnyxjbkb'],
        ];
    }

    /**
     * Data provider for faulty event ID's
     *
     * @return array
     */
    public function badEventIdProvider(): array
    {
        return [
            ['PHP-Leuven-Web-Innovation-Group-Fail', 'sctxfnyxjbkb'],
        ];
    }

    /**
     * A data provider for bad API keys
     *
     * @return array
     */
    public function badApiKeyProvider(): array
    {
        $faker = Factory::create();
        $apiKeyList = [];
        for ($i = 0; $i < 10; $i++) {
            $apiKeyList[] = [$faker->uuid];
        }
        return $apiKeyList;
    }

    /**
     * Testing that we receive an exception when we cannot connect
     * to the meetup service
     *
     * @return void
     *
     * @covers                   \DragonBe\Meetup\Consumer::__construct()
     * @covers                   \DragonBe\Meetup\Consumer::getGroup()
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Cannot connect with Meetup.com API
     * @dataProvider             badUriProvider
     */
    public function testConsumerThrowsExceptionWhenConnectionToApiFailsForGroup(string $uri)
    {

        $mock = new MockHandler(
            [
                new ConnectException(
                    'Error Communicating with Server',
                    new Request('GET', 'test')
                ),
            ]
        );

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $consumer = $this->getConsumer($client);
        $group = $consumer->getGroup($this->testGroup);

        $this->assertNull(
            $group,
            'Expected an empty connection, but it seems to have a value'
        );
    }

    /**
     * Testing that an exception is thrown when a given group was not found
     *
     * @return void
     *
     * @covers                   \DragonBe\Meetup\Consumer::__construct()
     * @covers                   \DragonBe\Meetup\Consumer::getGroup()
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Requested group was not found on Meetup.com
     */
    public function testConsumerThrowsExceptionWhenGroupDoesNotExists()
    {
        $mock = new MockHandler(
            [
                new ClientException(
                    'Group not found',
                    new Request('GET', 'test'),
                    new Response(404, ['Content-Legnth: 0'])
                ),
            ]
        );

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $consumer = $this->getConsumer($client);
        $group = $consumer->getGroup($this->testGroup . '-Fail');

        $this->assertNull(
            $group,
            'Expected an empty connection, but it seems to have a value'
        );
    }

    /**
     * Testing we can connect with the meetup API
     *
     * @return void
     *
     * @covers \DragonBe\Meetup\Consumer::__construct()
     * @covers \DragonBe\Meetup\Consumer::getGroup()
     */
    public function testConsumerCanConnectWithApiAndRetrieveGroup()
    {
        $testData = file_get_contents(__DIR__ . '/_files/meetup_group.json');
        $mock = new MockHandler(
            [
                new Response(
                    200,
                    ['Content-Type: application/json'],
                    $testData
                )
            ]
        );

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $consumer = $this->getConsumer($client);
        $group = $consumer->getGroup($this->testGroup);
        $this->assertInstanceOf(Group::class, $group);
    }

    /**
     * Testing that we receive an exception when we cannot connect
     * to the meetup service
     *
     * @return void
     *
     * @covers                   \DragonBe\Meetup\Consumer::__construct()
     * @covers                   \DragonBe\Meetup\Consumer::getGroupEvents()
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Cannot connect with Meetup.com API
     * @dataProvider             badUriProvider
     */
    public function testConsumerThrowsExceptionWhenConnectionToApiFailsForGroupEvents(string $uri)
    {
        $mock = new MockHandler(
            [
                new ConnectException(
                    'Error Communicating with Server',
                    new Request('GET', 'test')
                ),
            ]
        );

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $consumer = $this->getConsumer($client);
        $eventsList = $consumer->getGroupEvents($this->testGroup);

        $this->assertNull(
            $eventsList,
            'Expected an empty connection, but it seems to have a value'
        );
    }

    /**
     * Testing that an exception is thrown when a given group event list was
     * not found
     *
     * @return void
     *
     * @covers                   \DragonBe\Meetup\Consumer::__construct()
     * @covers                   \DragonBe\Meetup\Consumer::getGroupEvents()
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Requested group was not found on Meetup.com
     */
    public function testConsumerThrowsExceptionWhenGroupEventsDoesNotExists()
    {
        $mock = new MockHandler(
            [
                new ClientException(
                    'Group not found',
                    new Request('GET', 'test'),
                    new Response(404, ['Content-Legnth: 0'])
                ),
            ]
        );

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $consumer = $this->getConsumer($client);
        $group = $consumer->getGroupEvents($this->testGroup . '-Fail');

        $this->assertNull(
            $group,
            'Expected an empty connection, but it seems to have a value'
        );
    }

    /**
     * Testing we can connect with the meetup API and get group
     * events
     *
     * @return void
     *
     * @covers       \DragonBe\Meetup\Consumer::__construct()
     * @covers       \DragonBe\Meetup\Consumer::getGroupEvents()
     * @dataProvider eventIdProvider
     */
    public function testConsumerCanConnectWithApiAndRetrieveGroupEvents(string $eventId)
    {
        $testData = file_get_contents(
            __DIR__ . '/_files/meetup_group_upcoming.json'
        );
        $mock = new MockHandler(
            [
                new Response(
                    200,
                    ['Content-Type: application/json'],
                    $testData
                )
            ]
        );

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $consumer = $this->getConsumer($client);
        $events = $consumer->getGroupEvents($this->testGroup);
        $this->assertCount(12, $events);
    }

    /**
     * Testing that we receive an exception when we cannot connect
     * to the meetup service
     *
     * @return void
     *
     * @covers                   \DragonBe\Meetup\Consumer::__construct()
     * @covers                   \DragonBe\Meetup\Consumer::getGroupEvent()
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Cannot connect with Meetup.com API
     * @dataProvider             badUriProvider
     */
    public function testConsumerThrowsExceptionWhenConnectionToApiFailsForGroupEvent(string $uri)
    {
        $mock = new MockHandler(
            [
                new ConnectException(
                    'Error Communicating with Server',
                    new Request('GET', 'test')
                ),
            ]
        );

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $consumer = $this->getConsumer($client);
        $group = $consumer->getGroupEvent($this->testGroup, $this->testEventId);

        $this->assertNull(
            $group,
            'Expected an empty connection, but it seems to have a value'
        );
    }

    /**
     * Testing that an exception is thrown when a given group was not found
     *
     * @param string $groupName The name of the Meetup group
     * @param string $eventId   The ID of the Meetup group event
     *
     * @return void
     *
     * @covers                   \DragonBe\Meetup\Consumer::__construct()
     * @covers                   \DragonBe\Meetup\Consumer::getGroupEvent()
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Requested group was not found on Meetup.com
     * @dataProvider             badEventIdProvider
     */
    public function testConsumerThrowsExceptionWhenGroupEventDoesNotExists(string $groupName, string $eventId)
    {
        $mock = new MockHandler(
            [
                new ClientException(
                    'Group not found',
                    new Request('GET', 'test'),
                    new Response(404, ['Content-Legnth: 0'])
                ),
            ]
        );

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $consumer = $this->getConsumer($client);
        $group = $consumer->getGroupEvent($groupName, $eventId);

        $this->assertNull(
            $group,
            'Expected an empty connection, but it seems to have a value'
        );
    }

    /**
     * Testing we can connect with the meetup API and get a group
     * event
     *
     * @param string $groupName The name of the Meetup.com group
     * @param string $eventId   The ID for the event
     *
     * @return void
     *
     * @covers       \DragonBe\Meetup\Consumer::__construct()
     * @covers       \DragonBe\Meetup\Consumer::getGroupEvent()
     * @dataProvider eventIdProvider
     */
    public function testConsumerCanConnectWithApiAndRetrieveSingleGroupEvent(string $groupName, string $eventId)
    {
        $testData = file_get_contents(__DIR__ . '/_files/meetup_group_event.json');
        $mock = new MockHandler(
            [
                new Response(
                    200,
                    ['Content-Type: application/json'],
                    $testData
                )
            ]
        );

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $consumer = $this->getConsumer($client);
        $event = $consumer->getGroupEvent($groupName, $eventId);
        $this->assertSame($eventId, $event->getId());
    }



    /**
     * Testing that we receive an exception when we cannot connect
     * to the meetup service to retrieve Meetup.com group members
     *
     * @return void
     *
     * @covers                   \DragonBe\Meetup\Consumer::__construct()
     * @covers                   \DragonBe\Meetup\Consumer::getGroupMemberCollection()
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Cannot connect with Meetup.com API
     * @dataProvider             badUriProvider
     */
    public function testConsumerThrowsExceptionWhenConnectionToApiFailsForGroupMembers(string $uri)
    {
        $mock = new MockHandler(
            [
                new ConnectException(
                    'Error Communicating with Server',
                    new Request('GET', 'test')
                ),
            ]
        );

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $consumer = $this->getConsumer($client);
        $memberCollection = $consumer->getGroupMemberCollection($this->testGroup);

        $this->assertNull(
            $memberCollection,
            'Expected an empty connection, but it seems to have a value'
        );
    }

    /**
     * Testing that an exception is thrown when a given group was not found
     * when retrieving members
     *
     * @param string $groupName The name of the Meetup group
     *
     * @return void
     *
     * @covers                   \DragonBe\Meetup\Consumer::__construct()
     * @covers                   \DragonBe\Meetup\Consumer::getGroupMemberCollection()
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Requested group was not found on Meetup.com
     * @dataProvider             badEventIdProvider
     */
    public function testConsumerThrowsExceptionWhenGroupDoesNotExistsForMembers(string $groupName)
    {
        $mock = new MockHandler(
            [
                new ClientException(
                    'Group not found',
                    new Request('GET', 'test'),
                    new Response(404, ['Content-Legnth: 0'])
                ),
            ]
        );

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $consumer = $this->getConsumer($client);
        $members = $consumer->getGroupMemberCollection($groupName);

        $this->assertNull(
            $members,
            'Expected an empty connection, but it seems to have a value'
        );
    }

    /**
     * Test that we can retrieve a collection of group members
     *
     * @param string $groupName The name of the group
     *
     * @return void
     *
     * @covers       \DragonBe\Meetup\Consumer::__construct()
     * @covers       \DragonBe\Meetup\Consumer::getGroupMemberCollection()
     * @dataProvider eventIdProvider
     */
    public function testConsumerCanRetrieveGroupMemberCollection(string $groupName)
    {
        $testData = file_get_contents(__DIR__ . '/_files/meetup_group_members.json');
        $mock = new MockHandler(
            [
                new Response(
                    200,
                    ['Content-Type: application/json'],
                    $testData
                ),
            ]
        );
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $consumer = $this->getConsumer($client);
        $memberCollection = $consumer->getGroupMemberCollection($groupName);
        $this->assertInstanceOf(\Iterator::class, $memberCollection);
        $this->assertCount(200, $memberCollection);
    }

    /**
     * Tests that we capture exceptions when connection fails
     * while retrieving RSVP's for a group event
     *
     * @return void
     *
     * @dataProvider             eventIdProvider
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Cannot connect with Meetup.com API
     */
    public function testExceptionForConnectionFailureRetrievingGroupEventRsvps(
        string $groupName,
        string $eventId
    ) {
        $mockHandler = new MockHandler(
            [
                new ConnectException(
                    'Error Communicating with Server',
                    new Request('GET', 'test')
                ),
            ]
        );
        $handler = HandlerStack::create($mockHandler);
        $client = new Client(['handler' => $handler]);
        $consumer = $this->getConsumer($client);
        $rsvpCollection = $consumer->getGroupEventRsvps($groupName, $eventId);
        $this->assertNull(
            $rsvpCollection,
            'Expecting RSVP collection to be empty, contains values'
        );
    }

    /**
     * Tests that we capture exceptions when connection fails
     * while retrieving RSVP's for a group event
     *
     * @return void
     *
     * @dataProvider             eventIdProvider
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Requested group was not found on Meetup.com
     */
    public function testExceptionForGroupNotFound(
        string $groupName,
        string $eventId
    ) {
        $mock = new MockHandler(
            [
                new ClientException(
                    'Group not found',
                    new Request('GET', 'test'),
                    new Response(404, ['Content-Legnth: 0'])
                ),
            ]
        );

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $consumer = $this->getConsumer($client);
        $rsvpCollection = $consumer->getGroupEventRsvps($groupName, $eventId);

        $this->assertNull(
            $rsvpCollection,
            'Expecting RSVP collection to be empty, contains values'
        );
    }

    /**
     * Tests that we retrieve RSVP's for a Meetup.com group
     * event.
     *
     * @return void
     *
     * @dataProvider eventIdProvider
     * @covers       \DragonBe\Meetup\Consumer::getGroupEventRsvps()
     */
    public function testCanRetrieveRsvpsFromGroupEvent(
        string $groupName,
        string $eventId
    ) {
        $testData = file_get_contents(__DIR__ . '/_files/meetup_group_event_rsvps.json');
        $mock = new MockHandler(
            [
                new Response(
                    200,
                    ['Content-Type: application/json'],
                    $testData
                ),
            ]
        );
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $consumer = $this->getConsumer($client);
        $rsvpCollection = $consumer->getGroupEventRsvps($groupName, $eventId);
        $this->assertInstanceOf(\Iterator::class, $rsvpCollection);
        $this->assertCount(4, $rsvpCollection);
    }

}
