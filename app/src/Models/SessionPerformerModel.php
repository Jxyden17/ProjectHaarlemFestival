<?php

namespace App\Models;

class SessionPerformerModel
{
    public int $sessionId;
    public int $performerId;
    public ?SessionModel $session;
    public ?PerformerModel $performer;

    public function __construct(
        int $sessionId,
        int $performerId,
        ?SessionModel $session = null,
        ?PerformerModel $performer = null
    )
    {
        $this->sessionId = $sessionId;
        $this->performerId = $performerId;
        $this->session = $session;
        $this->performer = $performer;
    }
}
