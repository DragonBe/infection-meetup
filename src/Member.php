<?php


namespace DragonBe\Meetup;

class Member implements MemberInterface
{
    /**
     * The ID of a Meetup.com member
     *
     * @var int
     */
    protected $id;

    /**
     * The name of a Meetup.com member
     *
     * @var string
     */
    protected $name;

    /**
     * Member constructor.
     *
     * @param array $data Data for a Meetup.com member
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
