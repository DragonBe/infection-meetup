<?php


namespace DragonBe\Meetup\Hydrator;

/**
 * Interface HydratorInterface
 *
 * This interface provides a blueprint how we hydrate objects
 *
 * @package DragonBe\Meetup\Hydrator
 */
interface HydratorInterface
{
    /**
     * Hydrates data in an object and returns the populated object
     *
     * @param mixed $data   The data as an associative array
     * @param mixed $object The object model
     *
     * @return mixed
     */
    public function hydrate($data, $object);

    /**
     * Converts a given object into a specific datatype
     *
     * @param mixed $object
     *
     * @return mixed
     */
    public function extract($object);
}
