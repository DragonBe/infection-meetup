<?php


namespace DragonBe\Meetup\Factory;

interface FactoryInterface
{
    /**
     * The invocation of the factory returns the object
     *
     * @return mixed
     */
    public function __invoke();
}
