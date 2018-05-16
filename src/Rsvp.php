<?php


namespace DragonBe\Meetup;

class Rsvp implements RsvpInterface
{
    /**
     * The group
     *
     * @var GroupInterface
     */
    protected $group;

    /**
     * The event
     *
     * @var EventInterface
     */
    protected $event;

    /**
     * The member
     * @var MemberInterface
     */
    protected $member;
    /**
     * @inheritDoc
     */

    /**
     * Rsvp constructor.
     *
     * @param GroupInterface  $group  The group
     * @param EventInterface  $event  The event
     * @param MemberInterface $member The member
     */
    public function __construct(
        ? GroupInterface $group = null,
        ? EventInterface $event = null,
        ? MemberInterface $member = null
    ) {
        $this->group = $group;
        $this->event = $event;
        $this->member = $member;
    }

    public function getGroup(): GroupInterface
    {
        return $this->group;
    }

    /**
     * @inheritDoc
     */
    public function getEvent(): EventInterface
    {
        return $this->event;
    }

    /**
     * @inheritDoc
     */
    public function getMember(): MemberInterface
    {
        return $this->member;
    }
}
