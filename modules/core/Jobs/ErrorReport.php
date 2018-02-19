<?php

namespace PhpCrystal\Core\Jobs;

use PhpCrystal\Core\Services\Mailer;

class ErrorReport extends AbstractJob
{
    private $recipient;
    private $errorCode;
    private $errorMessage;

    /**
     *
     */
    public function __construct($email, $errorCode, $errorMessage)
    {
        $this->recipient = $email;
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
    }

    /**
     *
     */
    public function handle(Mailer $mailer)
    {
        try {
            $mailer->sendErrorReport($this->recipient, $this->errorCode, $this->errorMessage);
        } catch (\Exception $e) {
            $this->fail($e);
        }
    }
}