<?php


namespace DragonBe\Meetup\Factory;

use DragonBe\Meetup\Consumer;
use DragonBe\Meetup\Event;
use DragonBe\Meetup\Group;
use DragonBe\Meetup\Hydrator\JsonCollectionCompositHydrator;
use DragonBe\Meetup\Hydrator\JsonCollectionHydrator;
use DragonBe\Meetup\Hydrator\JsonHydrator;
use DragonBe\Meetup\Member;
use DragonBe\Meetup\Rsvp;
use GuzzleHttp\Client;

class ConsumerFactory implements FactoryInterface
{
    const MAX_ALLOWED_ARGUMENTS = 1;
    const DEFAULT_CLIENT_USER_AGENT = 'DragonBe\Infection-Meetup-0.0.1';
    const DEFAULT_CLIENT_ACCEPT = 'application/json';
    const DEFAULT_CLIENT_ORIGIN = 'http://localhost:9876';

    /**
     * @inheritdoc
     */
    public function __invoke()
    {
        $apiKey = '';
        $uri = Consumer::BASE_API_URI;
        if (self::MAX_ALLOWED_ARGUMENTS === func_num_args()) {
            $uri = func_get_arg(0);
        }
        $hydrator = new JsonHydrator();
        $collectionHydrator = new JsonCollectionHydrator();
        $compositHydrator = new JsonCollectionCompositHydrator();
        $group = new Group();
        $event = new Event();
        $member = new Member();
        $rsvp = new Rsvp();
        $client = new Client(
            [
                'base_uri' => $uri,
                'verify'   => true,
                'headers'  => [
                    'User-Agent' => self::DEFAULT_CLIENT_USER_AGENT,
                    'Accept'     => self::DEFAULT_CLIENT_ACCEPT,
                    'Origin'     => self::DEFAULT_CLIENT_ORIGIN,
                ],
            ]
        );
        return new Consumer(
            $hydrator,
            $collectionHydrator,
            $compositHydrator,
            $group,
            $event,
            $member,
            $rsvp,
            $client,
            $apiKey
        );
    }
}
