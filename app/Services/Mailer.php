<?php

namespace App\Services;

use App\Models\User;
use App;

use Illuminate\Support\Facades\Mail;

class Mailer
{
    private $attachmentsToRemove = [];

    /**
     *
    */
    public function __destruct()
    {
        foreach ($this->attachmentsToRemove as $filename) {
            @unlink($filename);
        }
    }

    /**
     * @return void
    */
    public function sendErrorReport($recipient, $errorCode, $errorMessage)
    {
        $data = [];
        $data['errCode'] = $errorCode;
        $data['errMessage'] = $errorMessage;

        Mail::send('email.support.error_report', $data, function ($mail) use($recipient) {
            $subject = sprintf('Error report for %s', env('APP_NAME'));
            $mail
                ->from(sprintf('no-reply@%s', env('COMPANY_DOMAIN')), env('COMPANY_NAME'))
                ->to($recipient)
                ->subject($subject);
        });
    }
}
