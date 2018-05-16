<?php


namespace DragonBe\Meetup;

interface MemberInterface
{
    /**
     * Retrieves the ID from a Meetup member
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Retrieves the name from a Meetup member
     *
     * @return string
     */
    public function getName(): string;
}
