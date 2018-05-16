<?php


namespace DragonBe\Meetup\Hydrator;

class JsonCollectionHydrator implements HydratorInterface
{
    /**
     * Hydrates JSON API data into an object
     *
     * @inheritdoc
     */
    public function hydrate($data, $object)
    {
        $hydrator = new ArrayHydrator();
        $objectData = \json_decode($data, true);
        $collection = new \splObjectStorage();
        foreach ($objectData as $entry) {
            $collection->attach($hydrator->hydrate($entry, $object));
        }
        return $collection;
    }

    /**
     * @inheritDoc
     */
    public function extract($object)
    {
        $arrayHydrator = new ArrayHydrator();
        $object->rewind();
        $data = [];
        while ($object->valid()) {
            $data[] = $arrayHydrator->extract($object->current());
            $object->next();
        }
        return \json_encode($data);
    }
}
