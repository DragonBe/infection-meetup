<?php


namespace DragonBe\Meetup\Hydrator;

class JsonHydrator implements HydratorInterface
{
    /**
     * Hydrates JSON API data into an object
     *
     * @inheritdoc
     */
    public function hydrate($data, $object)
    {
        $objectData = \json_decode($data, true);
        $objectClass = \get_class($object);
        return new $objectClass($objectData);
    }

    /**
     * @inheritDoc
     */
    public function extract($object)
    {
        $reflectionObject = new \ReflectionClass($object);
        $properties = $reflectionObject->getProperties();
        $data = [];
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $key = $property->getName();
            $data[$key] = $property->getValue($object);
        }
        return \json_encode($data);
    }
}
