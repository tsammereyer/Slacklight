<?php 

namespace Slacklight;

class Message extends Entity {

    private $userId;
    private $channelId;
    private $username;
    private $content;
    private $created;
    private $seen;
    private $favourite;

    public function __construct(int $id, int $userId, int $channelId, string $username, string $content,
                 string $created, int $seen, int $favourite) {
        parent::__construct($id);
        $this->userId = $userId;
        $this->channelId = $channelId;
        $this->username = $username;
        $this->content = $content;
        $this->created = $created;
        $this->seen = $seen;
        $this->favourite = $favourite;

    }

    public function getUserId() : int {
        return $this->userId;
    }

    public function getChannelId() : int {
        return $this->channelId;
    }

    public function getUsername() : string {
        return $this->username;
    }

    public function getContent() : string {
        return $this->content;
    }

    public function getCreated() : string {
        return $this->created;
    }

    public function getSeen() : string {
        return $this->seen;
    }
    
    public function getFavourite() : string {
        return $this->favourite;
    }

}