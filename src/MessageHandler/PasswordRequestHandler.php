<?php


namespace App\MessageHandler;

use App\Message\PasswordRequest;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PasswordRequestHandler implements MessageHandlerInterface
{



    /**
     * PasswordRequestHandler constructor.
     */
    public function __construct()
    {
    }

    public function __invoke(PasswordRequest $message)
    {
    }
}
