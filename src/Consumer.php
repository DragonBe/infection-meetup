<?php


namespace DragonBe\Meetup;

use DragonBe\Meetup\Hydrator\HydratorInterface;
use DragonBe\Meetup\Hydrator\JsonCollectionCompositHydrator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;

class Consumer
{
    const BASE_API_URI = 'https://api.meetup.com';

    /**
     * The GuzzleHttp\Client
     *
     * @var Client
     */
    protected $client;

    /**
     * The API key for Meetup.com
     *
     * @var string
     */
    protected $apiKey;

    /**
     * The data hydrator
     *
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * Data collection hydrator
     *
     * @var HydratorInterface
     */
    protected $collectionHydrator;

    /**
     * Data composit hydrator
     *
     * @var HydratorInterface
     */
    protected $collectionCompositHydrator;

    /**
     * A prototype for a Meetup group model
     *
     * @var GroupInterface
     */
    protected $groupPrototype;

    /**
     * A prototype for a Meetup group event
     *
     * @var EventInterface
     */
    protected $eventPrototype;

    /**
     * A prototype for a Meetup group member
     *
     * @var MemberInterface
     */
    protected $memberPrototype;

    /**
     * A prototype for a Meetup RSVP
     *
     * @var RsvpInterface
     */
    protected $rsvpPrototype;

    /**
     * Consumer constructor.
     *
     * @param HydratorInterface $hydrator           The hydrator for the data
     * @param HydratorInterface $collectionHydrator The hydrator for the data
     * @param HydratorInterface $compositHydrator   The hydrator for the data
     * @param GroupInterface    $group              Prototype for a Group model
     * @param EventInterface    $event              Prototype for an Event model
     * @param MemberInterface   $member             Prototype for a Member model
     * @param RsvpInterface     $rsvp               Prototype for a RSVP model
     * @param Client            $client             The GuzzleHttp\Client
     * @param string            $apiKey             The API key for Meetup.com
     */
    public function __construct(
        HydratorInterface $hydrator,
        HydratorInterface $collectionHydrator,
        JsonCollectionCompositHydrator $compositHydrator,
        GroupInterface $group,
        EventInterface $event,
        MemberInterface $member,
        RsvpInterface $rsvp,
        Client $client,
        string $apiKey = ''
    ) {
        $this->hydrator = $hydrator;
        $this->collectionHydrator = $collectionHydrator;
        $this->collectionCompositHydrator = $compositHydrator;
        $this->groupPrototype = $group;
        $this->eventPrototype = $event;
        $this->memberPrototype = $member;
        $this->rsvpPrototype = $rsvp;
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    /**
     * Gets the details of a given group
     *
     * @param string $groupName The name of the Meetup group
     *
     * @return Group
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function getGroup(string $groupName): Group
    {
        try {
            $res = $this->client->request(
                'GET',
                '/' . $groupName
            );
        } catch (ConnectException $connectException) {
            throw new \RuntimeException('Cannot connect with Meetup.com API');
        } catch (ClientException $clientException) {
            if (404 === (int) $clientException->getCode()) {
                throw new \InvalidArgumentException(
                    'Requested group was not found on Meetup.com'
                );
            }
        }
        $groupJson = (string) $res->getBody();
        $group = $this->hydrator->hydrate($groupJson, $this->groupPrototype);
        return $group;
    }

    /**
     * Gets the details of a given group
     *
     * @param string $groupName The name of the Meetup group
     *
     * @return \Iterator
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function getGroupEvents(string $groupName): \Iterator
    {
        try {
            $res = $this->client->request(
                'GET',
                sprintf('/%s/events', $groupName)
            );
        } catch (ConnectException $connectException) {
            throw new \RuntimeException('Cannot connect with Meetup.com API');
        } catch (ClientException $clientException) {
            if (404 === (int) $clientException->getCode()) {
                throw new \InvalidArgumentException(
                    'Requested group was not found on Meetup.com'
                );
            }
        }
        $eventJson = (string) $res->getBody();
        $eventsCollection = $this->collectionHydrator->hydrate($eventJson, $this->eventPrototype);
        return $eventsCollection;
    }

    /**
     * Gets the details of a given group event
     *
     * @param string $groupName The name of the Meetup group
     * @param string $eventId The ID of the Meetup event
     *
     * @return Event
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function getGroupEvent(string $groupName, string $eventId): Event
    {
        try {
            $res = $this->client->request(
                'GET',
                sprintf('/%s/events/%s', $groupName, $eventId)
            );
        } catch (ConnectException $connectException) {
            throw new \RuntimeException('Cannot connect with Meetup.com API');
        } catch (ClientException $clientException) {
            if (404 === (int) $clientException->getCode()) {
                throw new \InvalidArgumentException(
                    'Requested group was not found on Meetup.com'
                );
            }
        }
        $eventJson = (string) $res->getBody();
        $event = $this->hydrator->hydrate($eventJson, $this->eventPrototype);
        return $event;
    }

    public function getGroupMemberCollection(string $groupName): \Iterator
    {
        try {
            $res = $this->client->request(
                'GET',
                sprintf('/%s/members', $groupName)
            );
        } catch (ConnectException $connectException) {
            throw new \RuntimeException('Cannot connect with Meetup.com API');
        } catch (ClientException $clientException) {
            if (404 === (int) $clientException->getCode()) {
                throw new \InvalidArgumentException(
                    'Requested group was not found on Meetup.com'
                );
            }
        }

        $memberCollectionJson = (string) $res->getBody();
        $memberCollection = $this->collectionHydrator->hydrate($memberCollectionJson, $this->memberPrototype);
        return $memberCollection;
    }

    /**
     * Retrieve the RSVP's for a Meetup.com group event
     *
     * @param string $groupName The name of the Meetup.com group
     * @param string $eventId   ID of the event
     *
     * @return \Iterator
     */
    public function getGroupEventRsvps(string $groupName, string $eventId): \Iterator
    {
        try {
            $res = $this->client->request(
                'GET',
                sprintf('/%s/events/%s/rsvps', $groupName, $eventId)
            );
        } catch (ConnectException $connectException) {
            throw new \RuntimeException('Cannot connect with Meetup.com API');
        } catch (ClientException $clientException) {
            if (404 === (int) $clientException->getCode()) {
                throw new \InvalidArgumentException(
                    'Requested group was not found on Meetup.com'
                );
            }
        }

        $rsvpCollectionJson = (string) $res->getBody();
        $rsvpCollection = $this->collectionCompositHydrator->hydrate(
            $rsvpCollectionJson,
            $this->rsvpPrototype,
            [
                'group' => $this->groupPrototype,
                'event' => $this->eventPrototype,
                'member' => $this->memberPrototype,
            ]
        );
        return $rsvpCollection;
    }
}
