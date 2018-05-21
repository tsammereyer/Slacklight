<?php
namespace Slacklight;

class Topic extends Entity {

    private $channelId;
    private $name;

    public function __construct(int $id, int $channelId, string $name) {
        parent::__construct($id);
        $this->channelId = $channelId;
        $this->name = $name;
    }

    public function getChannelId() : int {
        return $this->channelId;
    }

    public function getName() : string {
        return $this->name;
    }


}