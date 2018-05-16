<?php


namespace DragonBe\Meetup;

interface EventInterface
{
    public function getId(): string;
    public function getRsvp(): int;
}
