<?php

namespace App\Message;

final class SendNewsletterMessage
{
    private $userId;
    private $newsId;

    public function __construct(int $userId, int $newsId)
    {
        $this->userId = $userId;
        $this->newsId = $newsId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
    public function getNewsId(): int
    {
        return $this->newsId;
    }
}