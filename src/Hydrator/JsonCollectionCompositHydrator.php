<?php


namespace DragonBe\Meetup\Hydrator;

class JsonCollectionCompositHydrator implements HydratorInterface
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
        if (3 !== func_num_args()) {
            throw new \RuntimeException('Composite hydrator expects a composit structure');
        }
        $composite = func_get_arg(2);
        foreach ($objectData as $entity) {
            $container = [];
            foreach ($composite as $key => $prototype) {
                $container[] = $hydrator->hydrate($entity[$key], $prototype);
            }
            $compositeObj = new $object(...$container);
            $collection->attach($compositeObj);
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
            $entry = $arrayHydrator->extract($object->current());
            foreach ($entry as $key => $subObj) {
                $data[] = $arrayHydrator->extract($subObj);
            }
            $object->next();
        }
        return \json_encode($data);
    }
}
