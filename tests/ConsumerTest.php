<?php


namespace DragonBe\Test\Meetup;


use DragonBe\Meetup\Consumer;
use DragonBe\Meetup\Factory\ConsumerFactory;
use DragonBe\Meetup\Group;
use DragonBe\Meetup\Hydrator\JsonHydrator;
use Faker\Factory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
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
     * Setting up the Consumer class before each test
     *
     * @return void
     */
    protected function setUp()
    {
        $this->apiKey = '336659596827465517a104415731e4c';
        $this->testGroup = 'PHP-Leuven-Web-Innovation-Group';
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
     * @return                   void
     * @covers                   \DragonBe\Meetup\Consumer::getGroup()
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Cannot connect with Meetup.com API
     * @dataProvider             badUriProvider
     */
    public function testConsumerThrowsExceptionWhenConnectionToApiFails(string $uri)
    {

        $hydrator = new JsonHydrator();
        $group = new Group();
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
        $consumer = new Consumer($hydrator, $group, $client, $this->apiKey);
        $group = $consumer->getGroup($this->testGroup);

        $this->assertNull(
            $group,
            'Expected an empty connection, but it seems to have a value'
        );
    }

    /**
     * Testing that an exception is thrown when a given group was not found
     *
     * @covers                   \DragonBe\Meetup\Consumer::getGroup()
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Requested group was not found on Meetup.com
     * @return                   void
     */
    public function testConsumerThrowsExceptionWhenGroupDoesNotExists()
    {
        $hydrator = new JsonHydrator();
        $group = new Group();
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
        $consumer = new Consumer($hydrator, $group, $client, $this->apiKey);
        $group = $consumer->getGroup($this->testGroup . '-Fail');

        $this->assertNull(
            $group,
            'Expected an empty connection, but it seems to have a value'
        );
    }

    /**
     * Testing we can connect with the meetup API
     *
     * @covers \DragonBe\Meetup\Consumer::getGroup()
     * @return void
     */
    public function testConsumerCanConnectWithApiAndRetrieveGroup()
    {
        $testData = file_get_contents(__DIR__ . '/_files/meetup_group.json');
        $hydrator = new JsonHydrator();
        $group = new Group();
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
        $consumer = new Consumer($hydrator, $group, $client, $this->apiKey);
        $group = $consumer->getGroup($this->testGroup);
        $this->assertInstanceOf(Group::class, $group);
    }
}
