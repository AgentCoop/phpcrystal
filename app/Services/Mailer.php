<?php

namespace App\Services;

use App\Models\User;
use App;

use Illuminate\Support\Facades\Mail;

use App\Component\Mvc\Controller\AbstractService;

/**
 * @Service("singleton", tag="mailer")
*/
class Mailer extends AbstractService
{
    const ERR_REPORT_TEMPLATE_VARNAME = 'email_error_report';

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

        $config = $this->getConfig();

        Mail::send($config->get(self::ERR_REPORT_TEMPLATE_VARNAME), $data, function ($mail) use($recipient) {
            $subject = sprintf('Error report for %s', env('APP_NAME'));
            $mail
                ->from(sprintf('no-reply@%s', env('COMPANY_DOMAIN')), env('COMPANY_NAME'))
                ->to($recipient)
                ->subject($subject);
        });
    }
}
