<?php

namespace App\Message;

final class CompleteProductDeletionMessage
{
    public function __construct(public int $id)
    {
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
    
}
