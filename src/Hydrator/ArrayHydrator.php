<?php


namespace DragonBe\Meetup\Hydrator;

class ArrayHydrator implements HydratorInterface
{
    /**
     * Hydrates JSON API data into an object
     *
     * @inheritdoc
     */
    public function hydrate($data, $object)
    {
        $objectClass = \get_class($object);
        return new $objectClass($data);
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
        return $data;
    }
}
