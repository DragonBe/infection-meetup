<?php


namespace DragonBe\Meetup;

interface GroupInterface
{
    /**
     * Retrieve the ID of the group
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Retrieve the name of the group
     *
     * @return string
     */
    public function getName(): string;
}
