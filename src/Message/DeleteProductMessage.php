<?php

namespace App\Message;

final class DeleteProductMessage
{
    /*
     * Add whatever properties & methods you need to hold the
     * data for this message class.
     */

    public function __construct(public int $id, public bool $deleteChildren = false)
    {
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isDeleteChildren(): bool
    {
        return $this->deleteChildren;
    }


}
