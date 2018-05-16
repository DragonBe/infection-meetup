<?php


namespace DragonBe\Meetup;

class Group implements GroupInterface
{
    /**
     * The ID of the group
     *
     * @var int
     */
    protected $id;

    /**
     * The name of the group
     *
     * @var string
     */
    protected $name;

    /**
     * Group constructor.
     *
     * @param array $data The data stream from Meetup.com
     */
    public function __construct(array $data = [])
    {
        $this->id = 0;
        if (array_key_exists('id', $data)) {
            $this->id = (int) $data['id'];
        }
        $this->name = '';
        if (array_key_exists('name', $data)) {
            $this->name = (string) $data['name'];
        }
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }
}
