<?php


namespace DragonBe\Meetup;

/**
 * Interface RsvpInterface
 *
 * @package DragonBe\Meetup
 */
interface RsvpInterface
{
    /**
     * Retrieve Meetup.com Group information
     *
     * @return GroupInterface
     */
    public function getGroup(): GroupInterface;

    /**
     * Retrieve Meetup.com Event information
     *
     * @return EventInterface
     */
    public function getEvent(): EventInterface;

    /**
     * Retrieve Meetup.com Member information
     *
     * @return MemberInterface
     */
    public function getMember(): MemberInterface;
}
