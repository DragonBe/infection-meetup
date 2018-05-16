<?php


namespace DragonBe\Meetup;

/**
 * Class Event
 *
 * This class provides a way to retrieve important
 * event information from Meetup.com
 *
 * @package DragonBe\Meetup
 */
class Event implements EventInterface
{
    /**
     * The ID of the event
     *
     * @var string
     */
    protected $id;

    /**
     * The number of subscribed members
     *
     * @var int
     */
    protected $rsvp;

    public function __construct(array $data = [])
    {
        $this->id = '';
        if (array_key_exists('id', $data)) {
            $this->id = (string) $data['id'];
        }
        $this->rsvp = 0;
        if (array_key_exists('yes_rsvp_count', $data)) {
            $this->rsvp = $data['yes_rsvp_count'];
        }
    }

    /**
     * @inheritdoc
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getRsvp(): int
    {
        return $this->rsvp;
    }
}
