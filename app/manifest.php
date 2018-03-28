<?php

use App\Services as Service;

$this->service(Service\Mailer::class);

    $this->set(Service\Mailer::ERR_REPORT_TEMPLATE_VARNAME, 'email.support.error_report');

$this->close();